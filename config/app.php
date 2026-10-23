<?php

date_default_timezone_set('Africa/Lagos');

if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

if (is_file(APP_ROOT . '/.env')) {
    $envLines = file(APP_ROOT . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($envLines === false) {
        throw new RuntimeException('Unable to read application environment configuration.');
    }

    foreach ($envLines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }

        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if ($name === '' || getenv($name) !== false) {
            continue;
        }

        if (strlen($value) >= 2 && (
            ($value[0] === '"' && $value[strlen($value) - 1] === '"') ||
            ($value[0] === "'" && $value[strlen($value) - 1] === "'")
        )) {
            $value = substr($value, 1, -1);
        }

        putenv($name . '=' . $value);
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

if (!defined('APP_URL')) {
    define('APP_URL', getenv('APP_URL') ?: 'http://localhost/LMV');
}

if (!defined('APP_ENV')) {
    define('APP_ENV', getenv('APP_ENV') ?: 'development');
}

if (!defined('UPLOAD_MAX_SIZE')) {
    define('UPLOAD_MAX_SIZE', getenv('UPLOAD_MAX_SIZE') ?: 2 * 1024 * 1024);
}

if (!defined('DEFAULT_ADMIN_EMAIL')) {
    define('DEFAULT_ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: 'admin@kasuwa360.local');
}

if (!defined('DEFAULT_ADMIN_PASSWORD')) {
    define('DEFAULT_ADMIN_PASSWORD', getenv('ADMIN_PASSWORD') ?: 'Admin@123');
}
