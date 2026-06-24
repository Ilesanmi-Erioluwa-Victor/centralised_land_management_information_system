<?php

namespace App\Models;

/**
 * Land transaction records and approval workflow updates.
 */
class Transaction extends BaseModel
{
    /**
     * Find a transaction by UUID.
     *
     * @param string $id
     * @return array<string,mixed>|null
     */
    public function findById(string $id): ?array
    {
        // Retrieve one transaction with plot and owner context.
        $stmt = $this->db->prepare('SELECT t.*, lp.plot_number, fo.full_name AS from_owner, too.full_name AS to_owner FROM transactions t JOIN land_plots lp ON lp.id = t.plot_id LEFT JOIN land_owners fo ON fo.id = t.from_owner_id JOIN land_owners too ON too.id = t.to_owner_id WHERE t.id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /**
     * List transactions.
     *
     * @param array<string,mixed> $filters
     * @param int $page
     * @return array<int,array<string,mixed>>
     */
    public function list(array $filters = [], int $page = 1): array
    {
        $pg = $this->pagination($page);
        // Retrieve transactions with plot and owner names.
        $stmt = $this->db->prepare('SELECT t.*, lp.plot_number, too.full_name AS to_owner FROM transactions t JOIN land_plots lp ON lp.id = t.plot_id JOIN land_owners too ON too.id = t.to_owner_id ORDER BY t.created_at DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $pg['limit'], \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $pg['offset'], \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Create a transaction.
     *
     * @param array<string,mixed> $data
     * @return string
     */
    public function create(array $data): string
    {
        // Insert a pending transaction and return its generated UUID.
        $stmt = $this->db->prepare('INSERT INTO transactions (plot_id, from_owner_id, to_owner_id, transaction_type, amount, currency, transaction_date, notes, status, created_by) VALUES (:plot_id, :from_owner_id, :to_owner_id, :transaction_type, :amount, :currency, :transaction_date, :notes, :status, :created_by) RETURNING id');
        $stmt->execute([
            'plot_id' => $data['plot_id'],
            'from_owner_id' => $data['from_owner_id'] ?: null,
            'to_owner_id' => $data['to_owner_id'],
            'transaction_type' => $data['transaction_type'],
            'amount' => $data['amount'] ?: null,
            'currency' => $data['currency'] ?? 'NGN',
            'transaction_date' => $data['transaction_date'],
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'created_by' => $data['created_by'] ?? null,
        ]);
        return (string) $stmt->fetchColumn();
    }

    /**
     * Update approval status and ownership records.
     *
     * @param string $id
     * @param string $status
     * @param string|null $approvedBy
     * @return bool
     */
    public function updateStatus(string $id, string $status, ?string $approvedBy): bool
    {
        $this->db->beginTransaction();
        try {
            // Update the transaction status first.
            $stmt = $this->db->prepare('UPDATE transactions SET status = :status, approved_by = :approved_by, approved_at = CASE WHEN :status_check = :approved THEN NOW() ELSE approved_at END WHERE id = :id');
            $stmt->execute(['id' => $id, 'status' => $status, 'status_check' => $status, 'approved_by' => $approvedBy, 'approved' => 'approved']);

            if ($status === 'approved') {
                $transaction = $this->findById($id);
                if ($transaction) {
                    // Close previous ownership and open the new current ownership.
                    $close = $this->db->prepare('UPDATE plot_ownership SET is_current = FALSE, end_date = CURRENT_DATE WHERE plot_id = :plot_id AND is_current = TRUE');
                    $close->execute(['plot_id' => $transaction['plot_id']]);
                    $open = $this->db->prepare('INSERT INTO plot_ownership (plot_id, owner_id, start_date, is_current) VALUES (:plot_id, :owner_id, CURRENT_DATE, TRUE)');
                    $open->execute(['plot_id' => $transaction['plot_id'], 'owner_id' => $transaction['to_owner_id']]);
                    $plot = $this->db->prepare('UPDATE land_plots SET status = :status, updated_at = NOW() WHERE id = :plot_id');
                    $plot->execute(['plot_id' => $transaction['plot_id'], 'status' => 'allocated']);
                }
            }
            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Get transactions for a plot.
     *
     * @param string $plotId
     * @return array<int,array<string,mixed>>
     */
    public function getByPlot(string $plotId): array
    {
        // Retrieve plot transaction history.
        $stmt = $this->db->prepare('SELECT * FROM transactions WHERE plot_id = :plot_id ORDER BY transaction_date DESC, created_at DESC');
        $stmt->execute(['plot_id' => $plotId]);
        return $stmt->fetchAll();
    }
}
