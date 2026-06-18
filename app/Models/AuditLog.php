<?php

namespace App\Models;

/**
 * Immutable audit trail records.
 */
class AuditLog extends BaseModel
{
    /**
     * Create an audit log entry.
     *
     * @param string|null $userId
     * @param string $action
     * @param string|null $entityType
     * @param string|null $entityId
     * @param mixed $oldData
     * @param mixed $newData
     * @param string|null $ip
     * @return bool
     */
    public function create(?string $userId, string $action, ?string $entityType, ?string $entityId, mixed $oldData, mixed $newData, ?string $ip): bool
    {
        // Persist a before/after audit trail entry.
        $stmt = $this->db->prepare('INSERT INTO audit_logs (user_id, action, entity_type, entity_id, old_data, new_data, ip_address) VALUES (:user_id, :action, :entity_type, :entity_id, :old_data, :new_data, :ip_address)');
        return $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_data' => $oldData === null ? null : json_encode($oldData),
            'new_data' => $newData === null ? null : json_encode($newData),
            'ip_address' => $ip,
        ]);
    }

    /**
     * List audit entries.
     *
     * @param array<string,mixed> $filters
     * @param int $page
     * @return array<int,array<string,mixed>>
     */
    public function list(array $filters = [], int $page = 1): array
    {
        $pg = $this->pagination($page, 50);
        // Retrieve audit log rows with actor names.
        $stmt = $this->db->prepare('SELECT a.*, u.full_name FROM audit_logs a LEFT JOIN users u ON u.id = a.user_id ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $pg['limit'], \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pg['offset'], \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
