<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\LandPlot;

/**
 * Handles reports dashboard and CSV export.
 */
class ReportController extends BaseController
{
    /** @return void */
    public function index(): void
    {
        $db = Database::connection();
        $counts = [];
        foreach (['land_plots' => 'plots', 'land_owners' => 'owners', 'transactions' => 'transactions', 'documents' => 'documents'] as $table => $key) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM {$table}");
            $stmt->execute();
            $counts[$key] = (int)$stmt->fetchColumn();
        }
        $this->view('reports/index', ['title' => 'Reports', 'counts' => $counts, 'types' => (new LandPlot())->countByType()]);
    }
    /** @return void */
    public function exportCsv(): void
    {
        $stmt = Database::connection()->prepare('SELECT plot_number, land_type, location, state, area_sqm, status, current_owner, ownership_start, transaction_count, document_count FROM land_summary_view ORDER BY plot_number');
        $stmt->execute();
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=land_report_' . date('Ymd') . '.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Plot No', 'Type', 'Location', 'State', 'Area', 'Status', 'Owner', 'Ownership Start', 'Transactions', 'Documents']);
        foreach ($stmt->fetchAll() as $row) {
            fputcsv($out, $row);
        }
    }
}
