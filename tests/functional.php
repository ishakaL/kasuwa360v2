<?php
putenv('DB_DRIVER=sqlite');
putenv('DB_SQLITE_PATH=' . dirname(__DIR__) . '/storage/kasuwa360-functional-test.sqlite');

require __DIR__ . '/../config/app.php';
require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Models/UserModel.php';
require __DIR__ . '/../app/Models/ShopModel.php';
require __DIR__ . '/../app/Models/CategoryModel.php';
require __DIR__ . '/../app/Models/ListingModel.php';

$db = Database::getConnection();
$db->exec('DELETE FROM listing_media');
$db->exec('DELETE FROM listings');
$db->exec('DELETE FROM shops');
$db->exec('DELETE FROM users');

$userModel = new UserModel();
$vendor = $userModel->create([
    'full_name' => 'Test Vendor',
    'phone' => '+2348000000001',
    'email' => 'vendor@example.com',
    'password_hash' => password_hash('Secret123', PASSWORD_BCRYPT),
    'role' => 'vendor',
    'status' => 'active',
]);

$loginUser = $userModel->findByCredentials('+2348000000001', 'Secret123');
if (!$loginUser || $loginUser['id'] !== $vendor['id']) {
    fwrite(STDERR, "Vendor login verification failed\n");
    exit(1);
}

$shopModel = new ShopModel();
$shop = $shopModel->create([
    'user_id' => (int) $vendor['id'],
    'shop_name' => 'Kasuwa Market',
    'slug' => 'kasuwa-market',
    'description' => 'Local trade shop',
    'phone' => '+2348000000002',
    'city' => 'Lagos',
    'state' => 'Lagos',
    'approval_status' => 'approved',
    'status' => 'active',
]);

$categoryModel = new CategoryModel();
$category = $categoryModel->active()[0];

$listingModel = new ListingModel();
$listing = $listingModel->create([
    'shop_id' => (int) $shop['id'],
    'category_id' => (int) $category['id'],
    'title' => 'Sample Product',
    'type' => 'product',
    'description' => 'Fresh sample listing',
    'price' => 2500,
    'currency' => 'NGN',
    'is_negotiable' => 1,
    'location' => 'Agege',
    'availability_status' => 'in_stock',
    'publication_status' => 'published',
    'approval_status' => 'approved',
    'stock_quantity' => 10,
]);

if ((int) $listing['shop_id'] !== (int) $shop['id']) {
    fwrite(STDERR, "Listing creation failed\n");
    exit(1);
}

$searchResults = $listingModel->getPublicSearch(['keyword' => 'Sample']);
$searchCount = $listingModel->countPublicSearch(['keyword' => 'Sample']);
if (count($searchResults) !== 1 || $searchCount !== 1) {
    fwrite(STDERR, "Public listing search verification failed\n");
    exit(1);
}

echo "Vendor login and listing flow verified successfully.\n";
