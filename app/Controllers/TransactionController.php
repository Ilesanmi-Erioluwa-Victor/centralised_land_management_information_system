<?php

namespace App\Controllers;

use App\Helpers\Flash;
use App\Helpers\Mailer;
use App\Models\AuditLog;
use App\Models\LandOwner;
use App\Models\LandPlot;
use App\Models\Transaction;

/**
 * Handles transaction logging and approval workflow.
 */
class TransactionController extends BaseController
{
    /** @return void */
    public function index(): void { $this->view('transactions/index', ['title' => 'Transactions', 'transactions' => (new Transaction())->list($_GET, (int)($_GET['page'] ?? 1))]); }
    /** @param string $id @return void */
    public function show(string $id): void { $this->view('transactions/show', ['title' => 'Transaction Detail', 'transaction' => (new Transaction())->findById($id)]); }
    /** @return void */
    public function create(): void { $this->view('transactions/create', ['title' => 'Log Transaction', 'plots' => (new LandPlot())->list([], 1), 'owners' => (new LandOwner())->list([], 1)]); }
    /** @return void */
    public function store(): void
    {
        $this->verifyPost();
        if (strtotime($_POST['transaction_date'] ?? 'tomorrow') > time()) {
            Flash::set('error', 'Transaction date cannot be in the future.');
            $this->redirect('/transactions/create');
        }
        $_POST['created_by'] = $_SESSION['user_id'] ?? null;
        $id = (new Transaction())->create($_POST);
        (new AuditLog())->create($_SESSION['user_id'] ?? null, 'transaction_created', 'transaction', $id, null, $_POST, $this->ip());
        $tx = (new Transaction())->findById($id);
        if ($tx) {
            $html = render_email('transaction_created', ['transaction' => $tx]);
            Mailer::send('owner@example.com', $tx['to_owner'] ?? 'Owner', 'Land Transaction Logged - Ref #' . substr($id, 0, 8), $html);
        }
        Flash::set('success', 'Transaction logged for approval.');
        $this->redirect('/transactions/' . $id);
    }
    /** @param string $id @return void */
    public function approve(string $id): void { $this->verifyPost(); (new Transaction())->updateStatus($id, 'approved', $_SESSION['user_id'] ?? null); (new AuditLog())->create($_SESSION['user_id'] ?? null, 'transaction_approved', 'transaction', $id, null, ['status' => 'approved'], $this->ip()); Mailer::send('owner@example.com', 'Owner', 'Transaction Approved', render_email('transaction_approved', ['transaction' => (new Transaction())->findById($id)])); Flash::set('success', 'Transaction approved and ownership updated.'); $this->redirect('/transactions/' . $id); }
    /** @param string $id @return void */
    public function reject(string $id): void { $this->verifyPost(); (new Transaction())->updateStatus($id, 'rejected', $_SESSION['user_id'] ?? null); (new AuditLog())->create($_SESSION['user_id'] ?? null, 'transaction_rejected', 'transaction', $id, null, ['status' => 'rejected'], $this->ip()); Flash::set('success', 'Transaction rejected.'); $this->redirect('/transactions/' . $id); }
}
