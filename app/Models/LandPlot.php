<?php

namespace App\Models;

/**
 * Land plot records and reporting queries.
 */
class LandPlot extends BaseModel
{
    /**
     * Find a plot by UUID.
     *
     * @param string $id
     * @return array<string,mixed>|null
     */
    public function findById(string $id): ?array
    {
        // Retrieve a single land plot.
        $stmt = $this->db->prepare('SELECT * FROM land_plots WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Find a plot by plot number.
     *
     * @param string $plotNumber
     * @return array<string,mixed>|null
     */
    public function findByPlotNumber(string $plotNumber): ?array
    {
        // Retrieve a plot by its unique public plot number.
        $stmt = $this->db->prepare('SELECT * FROM land_plots WHERE plot_number = :plot_number');
        $stmt->execute(['plot_number' => $plotNumber]);
        return $stmt->fetch() ?: null;
    }

    /**
     * List plots with filters.
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
        foreach (['land_type', 'status', 'state', 'lga'] as $field) {
            if (!empty($filters[$field])) {
                $where[] = "{$field} = :{$field}";
                $params[$field] = $filters[$field];
            }
        }
        if (!empty($filters['q'])) {
            $where[] = '(plot_number ILIKE :q OR location ILIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['area_min'])) {
            $where[] = 'area_sqm >= :area_min';
            $params['area_min'] = $filters['area_min'];
        }
        if (!empty($filters['area_max'])) {
            $where[] = 'area_sqm <= :area_max';
            $params['area_max'] = $filters['area_max'];
        }
        if (!empty($filters['date_from'])) {
            $where[] = 'created_at::date >= :date_from';
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = 'created_at::date <= :date_to';
            $params['date_to'] = $filters['date_to'];
        }
        // Retrieve filtered plots with pagination.
        $sql = 'SELECT * FROM land_plots WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
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
     * Create a land plot.
     *
     * @param array<string,mixed> $data
     * @return string
     */
    public function create(array $data): string
    {
        // Insert a land plot and return its generated UUID.
        $stmt = $this->db->prepare('INSERT INTO land_plots (plot_number, land_type, location, state, lga, area_sqm, coordinates, description, status, registered_by) VALUES (:plot_number, :land_type, :location, :state, :lga, :area_sqm, :coordinates, :description, :status, :registered_by) RETURNING id');
        $stmt->execute([
            'plot_number' => $data['plot_number'],
            'land_type' => $data['land_type'],
            'location' => $data['location'],
            'state' => $data['state'],
            'lga' => $data['lga'] ?? null,
            'area_sqm' => $data['area_sqm'] ?: null,
            'coordinates' => $data['coordinates'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'available',
            'registered_by' => $data['registered_by'] ?? null,
        ]);
        return (string) $stmt->fetchColumn();
    }

    /**
     * Update a land plot.
     *
     * @param string $id
     * @param array<string,mixed> $data
     * @return bool
     */
    public function update(string $id, array $data): bool
    {
        // Update mutable land plot fields.
        $stmt = $this->db->prepare('UPDATE land_plots SET plot_number = :plot_number, land_type = :land_type, location = :location, state = :state, lga = :lga, area_sqm = :area_sqm, coordinates = :coordinates, description = :description, status = :status, updated_at = NOW() WHERE id = :id');
        return $stmt->execute([
            'id' => $id,
            'plot_number' => $data['plot_number'],
            'land_type' => $data['land_type'],
            'location' => $data['location'],
            'state' => $data['state'],
            'lga' => $data['lga'] ?? null,
            'area_sqm' => $data['area_sqm'] ?: null,
            'coordinates' => $data['coordinates'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'available',
        ]);
    }

    /**
     * Soft-delete by revoking a plot.
     *
     * @param string $id
     * @return bool
     */
    public function softDelete(string $id): bool
    {
        // Preserve history by marking the plot revoked.
        $stmt = $this->db->prepare('UPDATE land_plots SET status = :status, updated_at = NOW() WHERE id = :id');
        return $stmt->execute(['id' => $id, 'status' => 'revoked']);
    }

    /**
     * Count plots by status.
     *
     * @return array<int,array<string,mixed>>
     */
    public function countByStatus(): array
    {
        // Aggregate plots by workflow status.
        $stmt = $this->db->prepare('SELECT status, COUNT(*) AS total FROM land_plots GROUP BY status ORDER BY status');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Count plots by land type.
     *
     * @return array<int,array<string,mixed>>
     */
    public function countByType(): array
    {
        // Aggregate plots by land classification.
        $stmt = $this->db->prepare('SELECT land_type, COUNT(*) AS total FROM land_plots GROUP BY land_type ORDER BY land_type');
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
