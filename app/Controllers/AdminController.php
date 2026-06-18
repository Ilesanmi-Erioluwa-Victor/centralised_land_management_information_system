<?php

namespace App\Controllers;

use App\Helpers\Flash;
use App\Models\AuditLog;
use App\Models\User;

/**
 * Handles user administration, audit trail, and health check.
 */
class AdminController extends BaseController
{
    /** @return void */
    public function users(): void { $this->view('admin/users', ['title' => 'Users', 'users' => (new User())->list()]); }
    /** @param string $id @return void */
    public function toggleUser(string $id): void { $this->verifyPost(); (new User())->toggleActive($id); (new AuditLog())->create($_SESSION['user_id'] ?? null, 'user_toggled', 'user', $id, null, null, $this->ip()); Flash::set('success', 'User status updated.'); $this->redirect('/admin/users'); }
    /** @param string $id @return void */
    public function changeRole(string $id): void { $this->verifyPost(); $user = (new User())->findById($id); if ($user) { (new User())->update($id, ['full_name' => $user['full_name'], 'role' => $_POST['role']]); } Flash::set('success', 'User role updated.'); $this->redirect('/admin/users'); }
    /** @return void */
    public function audit(): void { $this->view('admin/audit', ['title' => 'Audit Trail', 'logs' => (new AuditLog())->list($_GET, (int)($_GET['page'] ?? 1))]); }
    /** @return void */
    public function ping(): void { header('Content-Type: application/json'); echo json_encode(['ok' => true, 'service' => 'clmis', 'time' => date('c')]); }
}
