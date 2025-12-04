<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\EmailList;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ImportStatusController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get import status for a list.
     */
    public function index(Brand $brand, EmailList $list): JsonResponse
    {
        $this->authorize('view', $brand);
        
        abort_if($list->brand_id !== $brand->id, 404);

        $imports = \App\Models\SubscriberImport::where('list_id', $list->id)
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($import) {
                return [
                    'id' => $import->id,
                    'filename' => $import->filename,
                    'status' => $import->status,
                    'total_rows' => $import->total_rows,
                    'processed_rows' => $import->processed_rows,
                    'imported_count' => $import->imported_count,
                    'skipped_count' => $import->skipped_count,
                    'error_count' => $import->error_count,
                    'progress_percentage' => $import->progress_percentage,
                    'created_at' => $import->created_at->toIso8601String(),
                    'completed_at' => $import->completed_at?->toIso8601String(),
                ];
            });

        return response()->json([
            'imports' => $imports,
            'subscriber_count' => $list->subscriber_count,
            'active_subscriber_count' => $list->active_subscriber_count,
        ]);
    }
}
