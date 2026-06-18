<?php

namespace App\Models;

/**
 * Land owner CRUD and ownership lookups.
 */
class LandOwner extends BaseModel
{
    /**
     * Find an owner by UUID.
     *
     * @param string $id
     * @return array<string,mixed>|null
     */
    public function findById(string $id): ?array
    {
        // Retrieve a single owner.
        $stmt = $this->db->prepare('SELECT * FROM land_owners WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * List owners.
     *
     * @param array<string,mixed> $filters
     * @param int $page
     * @return array<int,array<string,mixed>>
     */
    public function list(array $filters = [], int $page = 1): array
    {
        $pg = $this->pagination($page);
        $where = ['1=1'];
        $params = [];
        if (!empty($filters['q'])) {
            $where[] = '(full_name ILIKE :q OR email ILIKE :q OR phone ILIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }
        // Retrieve filtered owners with pagination.
        $sql = 'SELECT * FROM land_owners WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $pg['limit'], \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pg['offset'], \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create an owner.
     *
     * @param array<string,mixed> $data
     * @return string
     */
    public function create(array $data): string
    {
        // Insert an owner and return its generated UUID.
        $stmt = $this->db->prepare('INSERT INTO land_owners (full_name, email, phone, national_id, address, owner_type) VALUES (:full_name, :email, :phone, :national_id, :address, :owner_type) RETURNING id');
        $stmt->execute([
            'full_name' => $data['full_name'],
            'email' => $data['email'] ?: null,
            'phone' => $data['phone'] ?: null,
            'national_id' => $data['national_id'] ?: null,
            'address' => $data['address'] ?: null,
            'owner_type' => $data['owner_type'] ?? 'individual',
        ]);
        return (string) $stmt->fetchColumn();
    }

    /**
     * Update an owner.
     *
     * @param string $id
     * @param array<string,mixed> $data
     * @return bool
     */
    public function update(string $id, array $data): bool
    {
        // Update owner contact and identity fields.
        $stmt = $this->db->prepare('UPDATE land_owners SET full_name = :full_name, email = :email, phone = :phone, national_id = :national_id, address = :address, owner_type = :owner_type, updated_at = NOW() WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'full_name' => $data['full_name'],
            'email' => $data['email'] ?: null,
            'phone' => $data['phone'] ?: null,
            'national_id' => $data['national_id'] ?: null,
            'address' => $data['address'] ?: null,
            'owner_type' => $data['owner_type'] ?? 'individual',
        ]);
    }

    /**
     * Get owner with linked plots.
     *
     * @param string $id
     * @return array{owner:array<string,mixed>|null,plots:array<int,array<string,mixed>>}
     */
    public function getWithPlots(string $id): array
    {
        $owner = $this->findById($id);
        // Retrieve all plots currently or historically linked to the owner.
        $stmt = $this->db->prepare('SELECT lp.*, po.start_date, po.end_date, po.is_current FROM plot_ownership po JOIN land_plots lp ON lp.id = po.plot_id WHERE po.owner_id = :id ORDER BY po.created_at DESC');
        $stmt->execute(['id' => $id]);
        return ['owner' => $owner, 'plots' => $stmt->fetchAll()];
    }
}
