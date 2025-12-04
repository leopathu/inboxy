<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\EmailList;
use App\Models\SubscriberImport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;

class ImportController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of imports for a list.
     */
    public function index(Brand $brand, EmailList $list): Response
    {
        $this->authorize('view', $brand);
        
        abort_if($list->brand_id !== $brand->id, 404);

        $imports = SubscriberImport::where('list_id', $list->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Imports/Index', [
            'brand' => $brand,
            'list' => $list,
            'imports' => $imports,
        ]);
    }

    /**
     * Display the specified import.
     */
    public function show(Brand $brand, EmailList $list, SubscriberImport $import): Response
    {
        $this->authorize('view', $brand);
        
        abort_if($list->brand_id !== $brand->id, 404);
        abort_if($import->list_id !== $list->id, 404);

        $import->load(['list', 'user']);

        return Inertia::render('Imports/Show', [
            'brand' => $brand,
            'list' => $list,
            'import' => $import,
        ]);
    }
}
