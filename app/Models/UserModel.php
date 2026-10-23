<?php

class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): array
    {
        $sql = 'INSERT INTO users (full_name, phone, email, password_hash, role, status, created_at, updated_at)
                VALUES (:full_name, :phone, :email, :password_hash, :role, :status, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':phone' => $data['phone'],
            ':email' => $data['email'] ?? null,
            ':password_hash' => $data['password_hash'],
            ':role' => $data['role'] ?? 'buyer',
            ':status' => $data['status'] ?? 'active',
        ]);

        $id = (int) $this->db->lastInsertId();
        return $this->findById($id);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmailOrPhone(string $identifier): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email OR phone = :phone LIMIT 1');
        $stmt->execute([
            ':email' => $identifier,
            ':phone' => $identifier,
        ]);
        return $stmt->fetch() ?: null;
    }

    public function findByCredentials(string $identifier, string $password): ?array
    {
        $user = $this->findByEmailOrPhone($identifier);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }
        return $user;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM users ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function getDashboardStats(): array
    {
        $stats = [];
        foreach (['buyer','vendor','admin'] as $role) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE role = :role');
            $stmt->execute([':role' => $role]);
            $stats[$role] = (int) $stmt->fetchColumn();
        }

        $statusQuery = 'SELECT COUNT(*) FROM users WHERE status != :status';
        $stmt = $this->db->prepare($statusQuery);
        $stmt->execute([':status' => 'active']);
        $stats['suspended'] = (int) $stmt->fetchColumn();

        return $stats;
    }

    public function updateProfile(int $id, array $data): bool
    {
        $sql = 'UPDATE users SET full_name = :full_name, phone = :phone, email = :email, avatar = :avatar, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':full_name' => $data['full_name'],
            ':phone' => $data['phone'],
            ':email' => $data['email'] ?? null,
            ':avatar' => $data['avatar'] ?? null,
            ':id' => $id,
        ]);
    }

    public function updatePassword(int $id, string $hash): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = :hash, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        return $stmt->execute([':hash' => $hash, ':id' => $id]);
    }

    public function setStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }
}
