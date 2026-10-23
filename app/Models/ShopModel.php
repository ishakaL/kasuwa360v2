<?php

class ShopModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): array
    {
        $sql = 'INSERT INTO shops (user_id, shop_name, slug, logo, description, phone, whatsapp, email, address, city, state, market_name, opening_hours, approval_status, status, created_at, updated_at)
                VALUES (:user_id, :shop_name, :slug, :logo, :description, :phone, :whatsapp, :email, :address, :city, :state, :market_name, :opening_hours, :approval_status, :status, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':user_id' => $data['user_id'],
            ':shop_name' => $data['shop_name'],
            ':slug' => $data['slug'],
            ':logo' => $data['logo'] ?? null,
            ':description' => $data['description'] ?? '',
            ':phone' => $data['phone'] ?? '',
            ':whatsapp' => $data['whatsapp'] ?? '',
            ':email' => $data['email'] ?? null,
            ':address' => $data['address'] ?? '',
            ':city' => $data['city'] ?? '',
            ':state' => $data['state'] ?? '',
            ':market_name' => $data['market_name'] ?? '',
            ':opening_hours' => $data['opening_hours'] ?? '',
            ':approval_status' => $data['approval_status'] ?? 'pending',
            ':status' => $data['status'] ?? 'active',
        ]);

        return $this->findById((int) $this->db->lastInsertId());
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM shops WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM shops WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1');
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM shops WHERE slug = :slug LIMIT 1');
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT s.*, u.full_name as owner_name FROM shops s LEFT JOIN users u ON u.id = s.user_id ORDER BY s.created_at DESC');
        return $stmt->fetchAll();
    }

    public function getApprovedList(): array
    {
        $stmt = $this->db->query("SELECT * FROM shops WHERE status = 'active' AND approval_status = 'approved' ORDER BY created_at DESC LIMIT 12");
        return $stmt->fetchAll();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE shops SET shop_name = :shop_name, slug = :slug, logo = :logo, description = :description, phone = :phone, whatsapp = :whatsapp, email = :email, address = :address, city = :city, state = :state, market_name = :market_name, opening_hours = :opening_hours, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':shop_name' => $data['shop_name'],
            ':slug' => $data['slug'],
            ':logo' => $data['logo'] ?? null,
            ':description' => $data['description'] ?? '',
            ':phone' => $data['phone'] ?? '',
            ':whatsapp' => $data['whatsapp'] ?? '',
            ':email' => $data['email'] ?? null,
            ':address' => $data['address'] ?? '',
            ':city' => $data['city'] ?? '',
            ':state' => $data['state'] ?? '',
            ':market_name' => $data['market_name'] ?? '',
            ':opening_hours' => $data['opening_hours'] ?? '',
            ':id' => $id,
        ]);
    }

    public function setApproval(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE shops SET approval_status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function setStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE shops SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM shops')->fetchColumn();
    }
}
