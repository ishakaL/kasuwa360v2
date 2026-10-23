<?php

class Database
{
    private static ?PDO $connection = null;
    private static ?string $driver = null;

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $driver = getenv('DB_DRIVER') ?: 'sqlite';
        if (!in_array($driver, ['mysql', 'sqlite'], true)) {
            throw new RuntimeException('DB_DRIVER must be set to either mysql or sqlite.');
        }

        if ($driver === 'mysql') {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                getenv('DB_HOST') ?: '127.0.0.1',
                getenv('DB_PORT') ?: '3306',
                getenv('DB_NAME') ?: 'kasuwa360',
            );
            $username = getenv('DB_USERNAME') ?: 'root';
            $password = getenv('DB_PASSWORD') ?: '';
            try {
                $pdo = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (Throwable $e) {
                error_log('MySQL connection failed: ' . $e->getMessage());
                throw new RuntimeException('Unable to connect to the configured MySQL database. Check the DB_* settings in .env.', 0, $e);
            }

            self::$connection = $pdo;
            self::$driver = $driver;
            self::ensureSchema($pdo, $driver);
            return $pdo;
        }

        $sqlitePath = getenv('DB_SQLITE_PATH') ?: APP_ROOT . '/storage/kasuwa360.sqlite';
        $pdo = new PDO('sqlite:' . $sqlitePath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');

        self::$connection = $pdo;
        self::$driver = $driver;
        self::ensureSchema($pdo, $driver);

        return $pdo;
    }

    public static function ensureSchema(?PDO $connection = null, ?string $driver = null): void
    {
        $connection ??= self::getConnection();
        $driver ??= self::$driver ?? (getenv('DB_DRIVER') ?: 'sqlite');
        $suffix = $driver === 'sqlite' ? '.sqlite' : '';

        foreach (['schema' . $suffix . '.sql', 'seed' . $suffix . '.sql'] as $file) {
            $path = APP_ROOT . '/database/' . $file;
            if (!is_file($path)) {
                throw new RuntimeException('Required database file is missing: ' . $file);
            }

            $sql = file_get_contents($path);
            if ($sql === false || trim($sql) === '') {
                throw new RuntimeException('Required database file is empty or unreadable: ' . $file);
            }

            foreach (explode(';', $sql) as $statement) {
                $statement = trim($statement);
                if ($statement !== '') {
                    $connection->exec($statement);
                }
            }
        }
    }
}
