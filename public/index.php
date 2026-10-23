<?php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Session.php';
require_once __DIR__ . '/../app/Core/Helpers.php';
require_once __DIR__ . '/../app/Models/UserModel.php';
require_once __DIR__ . '/../app/Models/ShopModel.php';
require_once __DIR__ . '/../app/Models/CategoryModel.php';
require_once __DIR__ . '/../app/Models/ListingModel.php';
require_once __DIR__ . '/../app/Models/AdminLogModel.php';
require_once __DIR__ . '/../app/Services/UploadService.php';

Session::start();
Database::getConnection();

function ensureDefaultAdmin(): void
{
    $userModel = new UserModel();
    $existing = $userModel->findByEmailOrPhone(DEFAULT_ADMIN_EMAIL);
    if ($existing) {
        return;
    }

    $userModel->create([
        'full_name' => 'System Administrator',
        'phone' => '+2348000000000',
        'email' => DEFAULT_ADMIN_EMAIL,
        'password_hash' => password_hash(DEFAULT_ADMIN_PASSWORD, PASSWORD_BCRYPT),
        'role' => 'admin',
        'status' => 'active',
    ]);
}

ensureDefaultAdmin();

function setFlash(string $type, string $message): void
{
    Session::flushMessage($type, $message);
}

function getFlashMessages(): array
{
    return Session::getFlash();
}

function csrfField(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(Session::csrf()) . '">';
}

function requireCsrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    $token = $_POST['_csrf'] ?? '';
    if (!Session::validateCsrf($token)) {
        setFlash('error', 'Your session expired. Please try again.');
        redirect('login');
    }
}

function routePath(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $base = getBaseUri();
    if ($base !== '' && strpos($uri, $base) === 0) {
        $uri = substr($uri, strlen($base));
    }
    if ($uri === '') {
        $uri = '/';
    }
    return $uri;
}

function userCanManageListing(array $listing, int $userId): bool
{
    $shopModel = new ShopModel();
    $shop = $shopModel->findById((int) ($listing['shop_id'] ?? 0));
    return $shop && (int) $shop['user_id'] === $userId;
}

function getListingImage(array $listing): string
{
    $media = new ListingModel();
    $items = $media->getMedia((int) $listing['id']);
    $primary = $items[0]['image_path'] ?? '';
    return $primary !== '' ? '/' . ltrim($primary, '/') : '/assets/img/placeholder.jpg';
}

function renderPage(string $view, array $params = []): void
{
    renderView($view, $params);
}

$route = routePath();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    requireCsrf();
}

$categories = (new CategoryModel())->active();
$searchFilters = [
    'keyword' => $_GET['keyword'] ?? '',
    'category' => $_GET['category'] ?? '',
    'type' => $_GET['type'] ?? '',
    'city' => $_GET['city'] ?? '',
    'state' => $_GET['state'] ?? '',
    'min_price' => $_GET['min_price'] ?? '',
    'max_price' => $_GET['max_price'] ?? '',
];

switch ($route) {
    case '/':
        $listingModel = new ListingModel();
        $featured = $listingModel->getVisibleListings(8);
        $shops = (new ShopModel())->getApprovedList();
        renderPage('homepage', ['featured' => $featured, 'shops' => $shops, 'categories' => $categories]);
        break;

    case '/register':
        if ($method === 'POST') {
            $fullName = trim((string) ($_POST['full_name'] ?? ''));
            $phone = trim((string) ($_POST['phone'] ?? ''));
            $email = trim((string) ($_POST['email'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $confirm = (string) ($_POST['confirm_password'] ?? '');
            $role = in_array($_POST['role'] ?? 'buyer', ['buyer', 'vendor'], true) ? $_POST['role'] : 'buyer';

            if ($fullName === '' || $phone === '' || $password === '' || $confirm === '') {
                setFlash('error', 'Full name, phone, and password are required.');
                renderPage('register', ['categories' => $categories]);
                break;
            }

            if ($password !== $confirm) {
                setFlash('error', 'Passwords do not match.');
                renderPage('register', ['categories' => $categories]);
                break;
            }

            $userModel = new UserModel();
            if ($userModel->findByEmailOrPhone($phone) || ($email !== '' && $userModel->findByEmailOrPhone($email))) {
                setFlash('error', 'An account already exists for that phone or email.');
                renderPage('register', ['categories' => $categories]);
                break;
            }

            $user = $userModel->create([
                'full_name' => $fullName,
                'phone' => $phone,
                'email' => $email !== '' ? $email : null,
                'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                'role' => $role,
                'status' => 'active',
            ]);
            Session::set('user', $user);
            setFlash('success', 'Your account has been created successfully.');
            if ($role === 'vendor') {
                redirect('dashboard');
            }
            redirect('marketplace');
        }

        renderPage('register', ['categories' => $categories]);
        break;

    case '/login':
        if ($method === 'POST') {
            $identifier = trim((string) ($_POST['identifier'] ?? ''));
            $password = (string) ($_POST['password'] ?? '');
            $userModel = new UserModel();
            $user = $userModel->findByCredentials($identifier, $password);
            if ($user === null) {
                setFlash('error', 'Invalid login details.');
                renderPage('login', ['categories' => $categories]);
                break;
            }

            if (($user['status'] ?? '') !== 'active') {
                setFlash('error', 'This account is suspended and cannot be used at the moment.');
                renderPage('login', ['categories' => $categories]);
                break;
            }

            Session::set('user', $user);
            session_regenerate_id(true);
            setFlash('success', 'Welcome back, ' . e($user['full_name']) . '.');
            if (($user['role'] ?? '') === 'admin') {
                redirect('admin/dashboard');
            }
            if (($user['role'] ?? '') === 'vendor') {
                redirect('dashboard');
            }
            redirect('marketplace');
        }

        renderPage('login', ['categories' => $categories]);
        break;

    case '/logout':
        Session::destroy();
        setFlash('success', 'You have been logged out securely.');
        redirect('/');
        break;

    case '/dashboard':
        requireVendor();
        $shopModel = new ShopModel();
        $user = currentUser();
        $shop = $shopModel->findByUserId((int) $user['id']);
        $listingModel = new ListingModel();
        $listings = $shop ? $listingModel->getByShop((int) $shop['id']) : [];
        renderPage('dashboard', ['categories' => $categories, 'shop' => $shop, 'listings' => $listings, 'user' => $user]);
        break;

    case '/vendor/shop':
        requireVendor();
        $shopModel = new ShopModel();
        $user = currentUser();
        $shop = $shopModel->findByUserId((int) $user['id']);

        if ($method === 'POST') {
            $slug = slugify((string) ($_POST['shop_name'] ?? ''));
            $data = [
                'user_id' => (int) $user['id'],
                'shop_name' => trim((string) ($_POST['shop_name'] ?? '')),
                'slug' => $slug,
                'description' => trim((string) ($_POST['description'] ?? '')),
                'phone' => trim((string) ($_POST['phone'] ?? '')),
                'whatsapp' => trim((string) ($_POST['whatsapp'] ?? '')),
                'email' => trim((string) ($_POST['email'] ?? '')),
                'address' => trim((string) ($_POST['address'] ?? '')),
                'city' => trim((string) ($_POST['city'] ?? '')),
                'state' => trim((string) ($_POST['state'] ?? '')),
                'market_name' => trim((string) ($_POST['market_name'] ?? '')),
                'opening_hours' => trim((string) ($_POST['opening_hours'] ?? '')),
            ];

            if ($shop) {
                $shopModel->update((int) $shop['id'], $data);
                setFlash('success', 'Your shop information was updated.');
            } else {
                $shop = $shopModel->create($data);
                setFlash('success', 'Your shop has been created.');
            }
            redirect('dashboard');
        }

        renderPage('shop-form', ['categories' => $categories, 'shop' => $shop]);
        break;

    case '/listing/new':
        requireVendor();
        $shopModel = new ShopModel();
        $user = currentUser();
        $shop = $shopModel->findByUserId((int) $user['id']);

        if (!$shop) {
            setFlash('error', 'Create your shop first before adding a listing.');
            redirect('vendor/shop');
        }

        if ($method === 'POST') {
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $data = [
                'shop_id' => (int) $shop['id'],
                'category_id' => $categoryId,
                'title' => trim((string) ($_POST['title'] ?? '')),
                'type' => in_array($_POST['type'] ?? 'product', ['product', 'service'], true) ? $_POST['type'] : 'product',
                'description' => trim((string) ($_POST['description'] ?? '')),
                'price' => (float) ($_POST['price'] ?? 0),
                'currency' => trim((string) ($_POST['currency'] ?? 'NGN')),
                'is_negotiable' => !empty($_POST['is_negotiable']) ? 1 : 0,
                'location' => trim((string) ($_POST['location'] ?? '')),
                'availability_status' => trim((string) ($_POST['availability_status'] ?? 'in_stock')),
                'publication_status' => 'draft',
                'approval_status' => 'pending',
                'stock_quantity' => (int) ($_POST['stock_quantity'] ?? 0),
                'pricing_model' => trim((string) ($_POST['pricing_model'] ?? 'fixed')),
                'service_area' => trim((string) ($_POST['service_area'] ?? '')),
            ];

            $listingModel = new ListingModel();
            $listing = $listingModel->create($data);
            $uploadService = new UploadService();
            $mediaFiles = $_FILES['images'] ?? [];
            if (!empty($mediaFiles['name'][0])) {
                foreach ($mediaFiles['name'] as $index => $name) {
                    if ($name === '') {
                        continue;
                    }
                    $file = [
                        'name' => $mediaFiles['name'][$index],
                        'tmp_name' => $mediaFiles['tmp_name'][$index],
                        'error' => $mediaFiles['error'][$index],
                        'size' => $mediaFiles['size'][$index],
                    ];
                    $result = $uploadService->save($file, 'listings');
                    if ($result['ok']) {
                        $listingModel->attachMedia((int) $listing['id'], $result['path'], $index === 0);
                    }
                }
            }
            setFlash('success', 'Your listing was created and sent for review.');
            redirect('dashboard');
        }

        renderPage('listing-form', ['categories' => $categories, 'listing' => null]);
        break;

    case '/listing/edit':
        requireVendor();
        $listingId = (int) ($_GET['id'] ?? 0);
        $listingModel = new ListingModel();
        $listing = $listingModel->findById($listingId);
        if (!$listing) {
            redirect('dashboard');
        }
        if (!userCanManageListing($listing, (int) currentUser()['id'])) {
            setFlash('error', 'You are not allowed to edit that listing.');
            redirect('dashboard');
        }

        if ($method === 'POST') {
            $data = [
                'category_id' => (int) ($_POST['category_id'] ?? $listing['category_id']),
                'title' => trim((string) ($_POST['title'] ?? $listing['title'])),
                'type' => in_array($_POST['type'] ?? $listing['type'], ['product', 'service'], true) ? $_POST['type'] : $listing['type'],
                'description' => trim((string) ($_POST['description'] ?? $listing['description'])),
                'price' => (float) ($_POST['price'] ?? $listing['price']),
                'currency' => trim((string) ($_POST['currency'] ?? $listing['currency'])),
                'is_negotiable' => !empty($_POST['is_negotiable']) ? 1 : 0,
                'location' => trim((string) ($_POST['location'] ?? $listing['location'])),
                'availability_status' => trim((string) ($_POST['availability_status'] ?? $listing['availability_status'])),
                'publication_status' => trim((string) ($_POST['publication_status'] ?? $listing['publication_status'])),
                'approval_status' => trim((string) ($_POST['approval_status'] ?? $listing['approval_status'])),
                'stock_quantity' => (int) ($_POST['stock_quantity'] ?? $listing['stock_quantity']),
                'pricing_model' => trim((string) ($_POST['pricing_model'] ?? $listing['pricing_model'])),
                'service_area' => trim((string) ($_POST['service_area'] ?? $listing['service_area'])),
            ];
            $listingModel->update((int) $listing['id'], $data);
            setFlash('success', 'Your listing was updated.');
            redirect('dashboard');
        }

        renderPage('listing-form', ['categories' => $categories, 'listing' => $listing]);
        break;

    case '/listing/delete':
        requireVendor();
        $listingId = (int) ($_GET['id'] ?? 0);
        $listingModel = new ListingModel();
        $listing = $listingModel->findById($listingId);
        if ($listing && userCanManageListing($listing, (int) currentUser()['id'])) {
            $listingModel->delete($listingId);
            setFlash('success', 'The listing was deleted.');
        }
        redirect('dashboard');
        break;

    case '/marketplace':
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 12;
        $offset = ($page - 1) * $limit;
        $listingModel = new ListingModel();
        $listings = $listingModel->getPublicSearch($searchFilters, $limit, $offset);
        $total = $listingModel->countPublicSearch($searchFilters);
        $totalPages = (int) ceil($total / $limit);
        renderPage('marketplace', ['categories' => $categories, 'listings' => $listings, 'filters' => $searchFilters, 'page' => $page, 'totalPages' => $totalPages, 'total' => $total]);
        break;

    default:
        if (preg_match('#^/shop/([a-zA-Z0-9-]+)$#', $route, $matches)) {
            $shopModel = new ShopModel();
            $shop = $shopModel->findBySlug($matches[1]);
            if (!$shop) {
                renderPage('not-found', ['categories' => $categories]);
                break;
            }
            $listingModel = new ListingModel();
            $shopListings = $listingModel->getByShop((int) $shop['id']);
            renderPage('shop-detail', ['shop' => $shop, 'listings' => $shopListings, 'categories' => $categories]);
            break;
        }

        if (preg_match('#^/listing/(\d+)$#', $route, $matches)) {
            $listingModel = new ListingModel();
            $listing = $listingModel->findById((int) $matches[1]);
            if (!$listing) {
                renderPage('not-found', ['categories' => $categories]);
                break;
            }
            if (($listing['publication_status'] ?? '') !== 'published' || ($listing['approval_status'] ?? '') !== 'approved') {
                renderPage('not-found', ['categories' => $categories]);
                break;
            }
            $media = $listingModel->getMedia((int) $listing['id']);
            renderPage('listing-detail', ['listing' => $listing, 'media' => $media, 'categories' => $categories]);
            break;
        }

        if (preg_match('#^/admin$#', $route)) {
            renderPage('admin-login', ['categories' => $categories]);
            break;
        }

        if (preg_match('#^/admin/login$#', $route)) {
            if ($method === 'POST') {
                $identifier = trim((string) ($_POST['identifier'] ?? ''));
                $password = (string) ($_POST['password'] ?? '');
                $user = (new UserModel())->findByCredentials($identifier, $password);
                if ($user && ($user['role'] ?? '') === 'admin') {
                    Session::set('user', $user);
                    session_regenerate_id(true);
                    redirect('admin/dashboard');
                }
                setFlash('error', 'Invalid administrator login credentials.');
            }
            renderPage('admin-login', ['categories' => $categories]);
            break;
        }

        if (preg_match('#^/admin/dashboard$#', $route)) {
            requireAdmin();
            $userModel = new UserModel();
            $shopModel = new ShopModel();
            $listingModel = new ListingModel();
            $logModel = new AdminLogModel();
            $stats = [
                'total_users' => count($userModel->getAll()),
                'total_vendors' => count(array_filter($userModel->getAll(), fn($user) => ($user['role'] ?? '') === 'vendor')),
                'total_shops' => $shopModel->countAll(),
                'total_listings' => $listingModel->countAll(),
                'published_listings' => $listingModel->countPublished(),
                'pending_listings' => $listingModel->countPending(),
            ];
            $recent = $logModel->recent();
            renderPage('admin-dashboard', ['categories' => $categories, 'stats' => $stats, 'recent' => $recent]);
            break;
        }

        if (preg_match('#^/admin/users$#', $route)) {
            requireAdmin();
            $users = (new UserModel())->getAll();
            renderPage('admin-users', ['categories' => $categories, 'users' => $users]);
            break;
        }

        if (preg_match('#^/admin/shops$#', $route)) {
            requireAdmin();
            $shops = (new ShopModel())->getAll();
            renderPage('admin-shops', ['categories' => $categories, 'shops' => $shops]);
            break;
        }

        if (preg_match('#^/admin/listings$#', $route)) {
            requireAdmin();
            $listings = (new ListingModel())->getVisibleListings(50);
            renderPage('admin-listings', ['categories' => $categories, 'listings' => $listings]);
            break;
        }

        if (preg_match('#^/admin/categories$#', $route)) {
            requireAdmin();
            $categories = (new CategoryModel())->all();
            renderPage('admin-categories', ['categories' => $categories]);
            break;
        }

        if (preg_match('#^/admin/approve-shop$#', $route) && $method === 'POST') {
            requireAdmin();
            $shopId = (int) ($_POST['shop_id'] ?? 0);
            (new ShopModel())->setApproval($shopId, 'approved');
            $admin = currentUser();
            (new AdminLogModel())->log((int) $admin['id'], 'approve_shop', 'Approved shop ID ' . $shopId);
            setFlash('success', 'Shop approved.');
            redirect('admin/shops');
        }

        if (preg_match('#^/admin/reject-shop$#', $route) && $method === 'POST') {
            requireAdmin();
            $shopId = (int) ($_POST['shop_id'] ?? 0);
            (new ShopModel())->setApproval($shopId, 'rejected');
            setFlash('success', 'Shop marked as rejected.');
            redirect('admin/shops');
        }

        if (preg_match('#^/admin/approve-listing$#', $route) && $method === 'POST') {
            requireAdmin();
            $listingId = (int) ($_POST['listing_id'] ?? 0);
            (new ListingModel())->setApproval($listingId, 'approved');
            setFlash('success', 'Listing approved.');
            redirect('admin/listings');
        }

        if (preg_match('#^/admin/reject-listing$#', $route) && $method === 'POST') {
            requireAdmin();
            $listingId = (int) ($_POST['listing_id'] ?? 0);
            (new ListingModel())->setApproval($listingId, 'rejected');
            setFlash('success', 'Listing rejected.');
            redirect('admin/listings');
        }

        if (preg_match('#^/admin/user-status$#', $route) && $method === 'POST') {
            requireAdmin();
            $userId = (int) ($_POST['user_id'] ?? 0);
            $status = in_array($_POST['status'] ?? 'active', ['active', 'suspended'], true) ? $_POST['status'] : 'active';
            (new UserModel())->setStatus($userId, $status);
            setFlash('success', 'User status updated.');
            redirect('admin/users');
        }

        if (preg_match('#^/admin/category/create$#', $route) && $method === 'POST') {
            requireAdmin();
            $name = trim((string) ($_POST['name'] ?? ''));
            if ($name !== '') {
                $categoryModel = new CategoryModel();
                $slug = slugify($name);
                $categoryModel->create(['name' => $name, 'slug' => $slug, 'status' => 'active']);
            }
            redirect('admin/categories');
        }

        renderPage('not-found', ['categories' => $categories]);
        break;
}
