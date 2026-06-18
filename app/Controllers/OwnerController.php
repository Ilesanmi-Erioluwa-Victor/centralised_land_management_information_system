<?php

namespace App\Controllers;

use App\Helpers\Flash;
use App\Models\AuditLog;
use App\Models\LandOwner;

/**
 * Handles land owner CRUD screens.
 */
class OwnerController extends BaseController
{
    /** @return void */
    public function index(): void { $this->view('owners/index', ['title' => 'Land Owners', 'owners' => (new LandOwner())->list($_GET, (int)($_GET['page'] ?? 1))]); }
    /** @param string $id @return void */
    public function show(string $id): void { $this->view('owners/show', ['title' => 'Owner Detail'] + (new LandOwner())->getWithPlots($id)); }
    /** @return void */
    public function create(): void { $this->view('owners/create', ['title' => 'Create Owner']); }
    /** @return void */
    public function store(): void { $this->verifyPost(); $id = (new LandOwner())->create($_POST); (new AuditLog())->create($_SESSION['user_id'] ?? null, 'owner_created', 'land_owner', $id, null, $_POST, $this->ip()); Flash::set('success', 'Owner created.'); $this->redirect('/owners/' . $id); }
    /** @param string $id @return void */
    public function edit(string $id): void { $this->view('owners/edit', ['title' => 'Edit Owner', 'owner' => (new LandOwner())->findById($id)]); }
    /** @param string $id @return void */
    public function update(string $id): void { $this->verifyPost(); $model = new LandOwner(); $old = $model->findById($id); $model->update($id, $_POST); (new AuditLog())->create($_SESSION['user_id'] ?? null, 'owner_updated', 'land_owner', $id, $old, $_POST, $this->ip()); Flash::set('success', 'Owner updated.'); $this->redirect('/owners/' . $id); }
}
