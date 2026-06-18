<?php

namespace App\Controllers;

/**
 * Renders the in-app user manual.
 */
class HelpController extends BaseController
{
    /**
     * Show help content.
     *
     * @return void
     */
    public function index(): void
    {
        $this->view('help', ['title' => 'Help']);
    }
}
