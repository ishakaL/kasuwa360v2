<?php

class AdminLogModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function log(int $adminId, string $action, string $details = ''): void
    {
        $stmt = $this->db->prepare('INSERT INTO admin_logs (admin_id, action, details, created_at) VALUES (:admin_id, :action, :details, CURRENT_TIMESTAMP)');
        $stmt->execute([
            ':admin_id' => $adminId,
            ':action' => $action,
            ':details' => $details,
        ]);
    }

    public function recent(): array
    {
        $stmt = $this->db->query('SELECT a.*, u.full_name as admin_name FROM admin_logs a LEFT JOIN users u ON u.id = a.admin_id ORDER BY a.created_at DESC LIMIT 10');
        return $stmt->fetchAll();
    }
}
