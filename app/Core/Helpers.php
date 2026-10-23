<?php

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    $base = APP_URL;
    $normalizedPath = '/' . ltrim($path, '/');
    if ($base === '' || $base === '/') {
        $location = $normalizedPath;
    } else {
        $location = rtrim($base, '/') . $normalizedPath;
    }
    header('Location: ' . $location);
    exit;
}

function currentUser(): ?array
{
    $user = Session::get('user');
    return is_array($user) ? $user : null;
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function isVendor(): bool
{
    $user = currentUser();
    return $user !== null && ($user['role'] ?? '') === 'vendor';
}

function isAdmin(): bool
{
    $user = currentUser();
    return $user !== null && ($user['role'] ?? '') === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        Session::flushMessage('error', 'Please log in to continue.');
        redirect('login');
    }
}

function requireVendor(): void
{
    requireLogin();
    if (!isVendor()) {
        Session::flushMessage('error', 'Only vendors can access this page.');
        redirect('dashboard');
    }
}

function requireAdmin(): void
{
    requireLogin();
    if (!isAdmin()) {
        Session::flushMessage('error', 'Unauthorized access.');
        redirect('login');
    }
}

function renderHeader(string $title = 'Kasuwa360'): void
{
    $user = currentUser();
    include APP_ROOT . '/resources/views/partials/header.php';
}

function renderFooter(): void
{
    include APP_ROOT . '/resources/views/partials/footer.php';
}

function renderView(string $view, array $params = []): void
{
    extract($params);
    include APP_ROOT . '/resources/views/' . $view . '.php';
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
    $value = trim((string) $value, '-');
    return $value ?: 'shop';
}

function formatPrice($value): string
{
    return '₦' . number_format((float) $value, 2);
}

function getBaseUri(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    $base = '/LMV';
    if (strpos($uri, $base) === 0) {
        return $base;
    }
    return '';
}

function normalizeRoute(string $route): string
{
    $base = getBaseUri();
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    if ($base !== '' && strpos($uri, $base) === 0) {
        $uri = substr($uri, strlen($base));
    }
    if ($uri === '') {
        $uri = '/';
    }
    if ($route === '') {
        return $uri;
    }
    return $uri;
}
