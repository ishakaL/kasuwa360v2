<?php

class ListingModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): array
    {
        $sql = 'INSERT INTO listings (shop_id, category_id, title, type, description, price, currency, is_negotiable, location, availability_status, publication_status, approval_status, stock_quantity, pricing_model, service_area, created_at, updated_at)
                VALUES (:shop_id, :category_id, :title, :type, :description, :price, :currency, :is_negotiable, :location, :availability_status, :publication_status, :approval_status, :stock_quantity, :pricing_model, :service_area, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':shop_id' => $data['shop_id'],
            ':category_id' => $data['category_id'],
            ':title' => $data['title'],
            ':type' => $data['type'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':currency' => $data['currency'] ?? 'NGN',
            ':is_negotiable' => (int) ($data['is_negotiable'] ?? 0),
            ':location' => $data['location'] ?? '',
            ':availability_status' => $data['availability_status'] ?? 'in_stock',
            ':publication_status' => $data['publication_status'] ?? 'draft',
            ':approval_status' => $data['approval_status'] ?? 'pending',
            ':stock_quantity' => $data['stock_quantity'] ?? 0,
            ':pricing_model' => $data['pricing_model'] ?? 'fixed',
            ':service_area' => $data['service_area'] ?? '',
        ]);

        $id = (int) $this->db->lastInsertId();
        return $this->findById($id);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT l.*, s.shop_name, s.slug AS shop_slug, s.phone AS shop_phone, s.whatsapp AS shop_whatsapp, s.city AS shop_city, s.state AS shop_state, c.name AS category_name FROM listings l LEFT JOIN shops s ON s.id = l.shop_id LEFT JOIN categories c ON c.id = l.category_id WHERE l.id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getByShop(int $shopId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM listings WHERE shop_id = :shop_id ORDER BY created_at DESC');
        $stmt->execute([':shop_id' => $shopId]);
        return $stmt->fetchAll();
    }

    public function getVisibleListings(int $limit = 12): array
    {
        $stmt = $this->db->prepare("SELECT l.*, s.shop_name, s.slug AS shop_slug, c.name AS category_name FROM listings l JOIN shops s ON s.id = l.shop_id JOIN categories c ON c.id = l.category_id WHERE l.publication_status = 'published' AND l.approval_status = 'approved' AND s.status = 'active' AND s.approval_status = 'approved' ORDER BY l.created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPublicSearch(array $filters = [], int $limit = 12, int $offset = 0): array
    {
        $query = "SELECT l.*, s.shop_name, s.slug AS shop_slug, c.name AS category_name FROM listings l JOIN shops s ON s.id = l.shop_id JOIN categories c ON c.id = l.category_id WHERE l.publication_status = 'published' AND l.approval_status = 'approved' AND s.status = 'active' AND s.approval_status = 'approved'";
        $params = [];

        if (!empty($filters['keyword'])) {
            $query .= ' AND (l.title LIKE :keyword_title OR l.description LIKE :keyword_description)';
            $keyword = '%' . trim($filters['keyword']) . '%';
            $params[':keyword_title'] = $keyword;
            $params[':keyword_description'] = $keyword;
        }
        if (!empty($filters['category'])) {
            $query .= ' AND c.slug = :category';
            $params[':category'] = trim($filters['category']);
        }
        if (!empty($filters['type'])) {
            $query .= ' AND l.type = :type';
            $params[':type'] = trim($filters['type']);
        }
        if (!empty($filters['city'])) {
            $query .= ' AND s.city LIKE :city';
            $params[':city'] = '%' . trim($filters['city']) . '%';
        }
        if (!empty($filters['state'])) {
            $query .= ' AND s.state LIKE :state';
            $params[':state'] = '%' . trim($filters['state']) . '%';
        }
        if (!empty($filters['min_price'])) {
            $query .= ' AND l.price >= :min_price';
            $params[':min_price'] = (float) $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $query .= ' AND l.price <= :max_price';
            $params[':max_price'] = (float) $filters['max_price'];
        }

        $query .= ' ORDER BY l.created_at DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countPublicSearch(array $filters = []): int
    {
        $query = "SELECT COUNT(*) FROM listings l JOIN shops s ON s.id = l.shop_id JOIN categories c ON c.id = l.category_id WHERE l.publication_status = 'published' AND l.approval_status = 'approved' AND s.status = 'active' AND s.approval_status = 'approved'";
        $params = [];

        if (!empty($filters['keyword'])) {
            $query .= ' AND (l.title LIKE :keyword_title OR l.description LIKE :keyword_description)';
            $keyword = '%' . trim($filters['keyword']) . '%';
            $params[':keyword_title'] = $keyword;
            $params[':keyword_description'] = $keyword;
        }
        if (!empty($filters['category'])) {
            $query .= ' AND c.slug = :category';
            $params[':category'] = trim($filters['category']);
        }
        if (!empty($filters['type'])) {
            $query .= ' AND l.type = :type';
            $params[':type'] = trim($filters['type']);
        }
        if (!empty($filters['city'])) {
            $query .= ' AND s.city LIKE :city';
            $params[':city'] = '%' . trim($filters['city']) . '%';
        }
        if (!empty($filters['state'])) {
            $query .= ' AND s.state LIKE :state';
            $params[':state'] = '%' . trim($filters['state']) . '%';
        }
        if (!empty($filters['min_price'])) {
            $query .= ' AND l.price >= :min_price';
            $params[':min_price'] = (float) $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $query .= ' AND l.price <= :max_price';
            $params[':max_price'] = (float) $filters['max_price'];
        }

        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function update(int $id, array $data): bool
    {
        $sql = 'UPDATE listings SET title = :title, category_id = :category_id, type = :type, description = :description, price = :price, currency = :currency, is_negotiable = :is_negotiable, location = :location, availability_status = :availability_status, publication_status = :publication_status, approval_status = :approval_status, stock_quantity = :stock_quantity, pricing_model = :pricing_model, service_area = :service_area, updated_at = CURRENT_TIMESTAMP WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':title' => $data['title'],
            ':category_id' => $data['category_id'],
            ':type' => $data['type'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':currency' => $data['currency'] ?? 'NGN',
            ':is_negotiable' => (int) ($data['is_negotiable'] ?? 0),
            ':location' => $data['location'] ?? '',
            ':availability_status' => $data['availability_status'] ?? 'in_stock',
            ':publication_status' => $data['publication_status'] ?? 'draft',
            ':approval_status' => $data['approval_status'] ?? 'pending',
            ':stock_quantity' => $data['stock_quantity'] ?? 0,
            ':pricing_model' => $data['pricing_model'] ?? 'fixed',
            ':service_area' => $data['service_area'] ?? '',
            ':id' => $id,
        ]);
    }

    public function setApproval(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE listings SET approval_status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM listings WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function countAll(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM listings')->fetchColumn();
    }

    public function countPublished(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM listings WHERE publication_status = 'published'")->fetchColumn();
    }

    public function countPending(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM listings WHERE approval_status = "pending"')->fetchColumn();
    }

    public function attachMedia(int $listingId, string $path, bool $isPrimary = false): void
    {
        $stmt = $this->db->prepare('INSERT INTO listing_media (listing_id, image_path, is_primary, created_at) VALUES (:listing_id, :image_path, :is_primary, CURRENT_TIMESTAMP)');
        $stmt->execute([
            ':listing_id' => $listingId,
            ':image_path' => $path,
            ':is_primary' => $isPrimary ? 1 : 0,
        ]);
    }

    public function getMedia(int $listingId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM listing_media WHERE listing_id = :listing_id ORDER BY is_primary DESC, id ASC');
        $stmt->execute([':listing_id' => $listingId]);
        return $stmt->fetchAll();
    }
}
