<?php

namespace App\Controllers;

use App\Helpers\Flash;
use App\Models\AuditLog;
use App\Models\Document;

/**
 * Handles document upload, listing, download, and deletion.
 */
class DocumentController extends BaseController
{
    /** @return void */
    public function index(): void { $this->view('documents/index', ['title' => 'Documents', 'documents' => (new Document())->list($_GET)]); }
    /** @return void */
    public function upload(): void { $this->view('documents/upload', ['title' => 'Upload Document']); }
    /** @return void */
    public function store(): void
    {
        $this->verifyPost();
        $file = $_FILES['document'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK || $file['size'] > 10 * 1024 * 1024) {
            Flash::set('error', 'Upload a valid file up to 10MB.');
            $this->redirect('/documents/upload');
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowed = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'];
        if (!isset($allowed[$mime])) {
            Flash::set('error', 'Allowed files: PDF, JPG, PNG, DOCX.');
            $this->redirect('/documents/upload');
        }
        $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
        $target = dirname(__DIR__, 2) . '/public/uploads/' . $name;
        move_uploaded_file($file['tmp_name'], $target);
        $id = (new Document())->create($_POST + ['file_name' => $file['name'], 'file_path' => '/uploads/' . $name, 'file_size' => $file['size'], 'mime_type' => $mime, 'uploaded_by' => $_SESSION['user_id'] ?? null]);
        (new AuditLog())->create($_SESSION['user_id'] ?? null, 'document_uploaded', 'document', $id, null, ['file_name' => $file['name']], $this->ip());
        Flash::set('success', 'Document uploaded.');
        $this->redirect('/documents');
    }
    /** @param string $id @return void */
    public function download(string $id): void
    {
        $doc = (new Document())->findById($id);
        $path = $doc ? dirname(__DIR__, 2) . '/public' . $doc['file_path'] : '';
        if (!$doc || !is_file($path)) {
            http_response_code(404);
            view('404', ['title' => 'Not Found'], 'auth');
            return;
        }
        header('Content-Type: ' . $doc['mime_type']);
        header('Content-Disposition: attachment; filename="' . basename($doc['file_name']) . '"');
        readfile($path);
    }
    /** @param string $id @return void */
    public function delete(string $id): void { $this->verifyPost(); $doc = (new Document())->findById($id); if ($doc) { @unlink(dirname(__DIR__, 2) . '/public' . $doc['file_path']); (new Document())->delete($id); } (new AuditLog())->create($_SESSION['user_id'] ?? null, 'document_deleted', 'document', $id, $doc, null, $this->ip()); Flash::set('success', 'Document deleted.'); $this->redirect('/documents'); }
}
