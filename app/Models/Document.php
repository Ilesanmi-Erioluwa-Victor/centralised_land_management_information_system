<?php

namespace App\Models;

/**
 * Uploaded document metadata and lookup queries.
 */
class Document extends BaseModel
{
    /**
     * Find a document by UUID.
     *
     * @param string $id
     * @return array<string,mixed>|null
     */
    public function findById(string $id): ?array
    {
        // Retrieve a single document record.
        $stmt = $this->db->prepare('SELECT * FROM documents WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * List documents.
     *
     * @param array<string,mixed> $filters
     * @return array<int,array<string,mixed>>
     */
    public function list(array $filters = []): array
    {
        // Retrieve recent documents with optional associations.
        $stmt = $this->db->prepare('SELECT d.*, lp.plot_number, lo.full_name AS owner_name FROM documents d LEFT JOIN land_plots lp ON lp.id = d.plot_id LEFT JOIN land_owners lo ON lo.id = d.owner_id ORDER BY d.created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create document metadata.
     *
     * @param array<string,mixed> $data
     * @return string
     */
    public function create(array $data): string
    {
        // Insert uploaded file metadata and return its generated UUID.
        $stmt = $this->db->prepare('INSERT INTO documents (plot_id, owner_id, transaction_id, doc_type, file_name, file_path, file_size, mime_type, uploaded_by) VALUES (:plot_id, :owner_id, :transaction_id, :doc_type, :file_name, :file_path, :file_size, :mime_type, :uploaded_by) RETURNING id');
        $stmt->execute([
            'plot_id' => $data['plot_id'] ?: null,
            'owner_id' => $data['owner_id'] ?: null,
            'transaction_id' => $data['transaction_id'] ?: null,
            'doc_type' => $data['doc_type'],
            'file_name' => $data['file_name'],
            'file_path' => $data['file_path'],
            'file_size' => $data['file_size'],
            'mime_type' => $data['mime_type'],
            'uploaded_by' => $data['uploaded_by'] ?? null,
        ]);
        return (string) $stmt->fetchColumn();
    }

    /**
     * Delete document metadata.
     *
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool
    {
        // Remove a document metadata record.
        $stmt = $this->db->prepare('DELETE FROM documents WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Get documents for a plot.
     *
     * @param string $plotId
     * @return array<int,array<string,mixed>>
     */
    public function getByPlot(string $plotId): array
    {
        // Retrieve documents linked to a land plot.
        $stmt = $this->db->prepare('SELECT * FROM documents WHERE plot_id = :plot_id ORDER BY created_at DESC');
        $stmt->execute(['plot_id' => $plotId]);
        return $stmt->fetchAll();
    }
}
