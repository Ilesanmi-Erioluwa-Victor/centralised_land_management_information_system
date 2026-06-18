<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\AuditLog;
use App\Models\LandPlot;

/**
 * Renders the authenticated dashboard.
 */
class DashboardController extends BaseController
{
    /**
     * Show KPI cards, charts, and recent activity.
     *
     * @return void
     */
    public function index(): void
    {
        $db = Database::connection();
        // Retrieve headline dashboard counts.
        $counts = [];
        foreach (['land_plots' => 'plots', 'land_owners' => 'owners', 'transactions' => 'transactions', 'documents' => 'documents'] as $table => $key) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM {$table}");
            $stmt->execute();
            $counts[$key] = (int) $stmt->fetchColumn();
        }
        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'counts' => $counts,
            'types' => (new LandPlot())->countByType(),
            'activity' => (new AuditLog())->list([], 1),
        ]);
    }
}
