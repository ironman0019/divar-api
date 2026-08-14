<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ApiDocumentationController extends Controller
{
    /**
     * Display the API documentation page.
     */
    public function index()
    {
        return view('admin.api-docs.index', [
            'baseUrl' => url('/api/V1'),
            'scrambleDocsUrl' => url('/docs/api'),
        ]);
    }
}
