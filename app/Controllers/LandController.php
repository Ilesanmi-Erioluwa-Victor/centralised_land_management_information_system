<?php

namespace App\Controllers;

use App\Helpers\Flash;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\LandPlot;
use App\Models\Transaction;

/**
 * Handles land record listing, creation, editing, search, and detail views.
 */
class LandController extends BaseController
{
    /**
     * List land plots.
     *
     * @return void
     */
    public function index(): void
    {
        $this->view('land/index', ['title' => 'Land Records', 'plots' => (new LandPlot())->list($_GET, (int)($_GET['page'] ?? 1))]);
    }

    /**
     * Show one land plot.
     *
     * @param string $id
     * @return void
     */
    public function show(string $id): void
    {
        $plot = (new LandPlot())->findById($id);
        if (!$plot) {
            view('404', ['title' => 'Not Found'], 'auth');
            return;
        }
        $this->view('land/show', ['title' => 'Plot ' . $plot['plot_number'], 'plot' => $plot, 'documents' => (new Document())->getByPlot($id), 'transactions' => (new Transaction())->getByPlot($id)]);
    }

    /**
     * Show create form.
     *
     * @return void
     */
    public function create(): void
    {
        $this->view('land/create', ['title' => 'Register Plot']);
    }

    /**
     * Store a new land plot.
     *
     * @return void
     */
    public function store(): void
    {
        $this->verifyPost();
        if ((new LandPlot())->findByPlotNumber($_POST['plot_number'] ?? '')) {
            Flash::set('error', 'Plot number already exists.');
            $this->redirect('/land/create');
        }
        $_POST['registered_by'] = $_SESSION['user_id'] ?? null;
        $id = (new LandPlot())->create($_POST);
        (new AuditLog())->create($_SESSION['user_id'] ?? null, 'plot_created', 'land_plot', $id, null, $_POST, $this->ip());
        Flash::set('success', 'Land plot registered.');
        $this->redirect('/land/' . $id);
    }

    /**
     * Show edit form.
     *
     * @param string $id
     * @return void
     */
    public function edit(string $id): void
    {
        $this->view('land/edit', ['title' => 'Edit Plot', 'plot' => (new LandPlot())->findById($id)]);
    }

    /**
     * Update an existing plot.
     *
     * @param string $id
     * @return void
     */
    public function update(string $id): void
    {
        $this->verifyPost();
        $model = new LandPlot();
        $old = $model->findById($id);
        $model->update($id, $_POST);
        (new AuditLog())->create($_SESSION['user_id'] ?? null, 'plot_updated', 'land_plot', $id, $old, $_POST, $this->ip());
        Flash::set('success', 'Land plot updated.');
        $this->redirect('/land/' . $id);
    }

    /**
     * Revoke a plot.
     *
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        $this->verifyPost();
        $old = (new LandPlot())->findById($id);
        (new LandPlot())->softDelete($id);
        (new AuditLog())->create($_SESSION['user_id'] ?? null, 'plot_revoked', 'land_plot', $id, $old, ['status' => 'revoked'], $this->ip());
        Flash::set('success', 'Land plot marked as revoked.');
        $this->redirect('/land');
    }

    /**
     * Advanced land search.
     *
     * @return void
     */
    public function search(): void
    {
        $this->view('land/search', ['title' => 'Advanced Search', 'plots' => (new LandPlot())->list($_GET, (int)($_GET['page'] ?? 1))]);
    }
}
