<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\EmailList;
use App\Models\Subscriber;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SubscriberController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of subscribers for a list.
     */
    public function index(Brand $brand, EmailList $list): Response
    {
        $this->authorize('view', $brand);
        abort_if($list->brand_id !== $brand->id, 404);

        $subscribers = Subscriber::where('list_id', $list->id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return Inertia::render('Subscribers/Index', [
            'brand' => $brand,
            'list' => $list,
            'subscribers' => $subscribers,
        ]);
    }

    /**
     * Show the form for creating a new subscriber.
     */
    public function create(Brand $brand, EmailList $list): Response
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);

        $customFields = $list->customFields()->active()->ordered()->get();

        return Inertia::render('Subscribers/Create', [
            'brand' => $brand,
            'list' => $list,
            'customFields' => $customFields,
        ]);
    }

    /**
     * Store a newly created subscriber.
     */
    public function store(Request $request, Brand $brand, EmailList $list): RedirectResponse
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);

        $rules = [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('subscribers')->where(function ($query) use ($list) {
                    return $query->where('list_id', $list->id);
                }),
            ],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'custom_fields' => ['nullable', 'array'],
            'status' => ['required', Rule::in([
                Subscriber::STATUS_SUBSCRIBED,
                Subscriber::STATUS_PENDING,
            ])],
        ];

        $validated = $request->validate($rules);

        $subscriber = Subscriber::create([
            'list_id' => $list->id,
            'email' => $validated['email'],
            'first_name' => $validated['first_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'custom_fields' => $validated['custom_fields'] ?? [],
            'status' => $validated['status'],
            'subscribed_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        $list->updateSubscriberCounts();

        return redirect()
            ->route('brands.lists.show', [$brand, $list])
            ->with('success', 'Subscriber added successfully.');
    }

    /**
     * Display the specified subscriber.
     */
    public function show(Brand $brand, EmailList $list, Subscriber $subscriber): Response
    {
        $this->authorize('view', $brand);
        abort_if($list->brand_id !== $brand->id, 404);
        abort_if($subscriber->list_id !== $list->id, 404);

        return Inertia::render('Subscribers/Show', [
            'brand' => $brand,
            'list' => $list,
            'subscriber' => $subscriber,
        ]);
    }

    /**
     * Show the form for editing the specified subscriber.
     */
    public function edit(Brand $brand, EmailList $list, Subscriber $subscriber): Response
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);
        abort_if($subscriber->list_id !== $list->id, 404);

        $customFields = $list->customFields()->active()->ordered()->get();

        return Inertia::render('Subscribers/Edit', [
            'brand' => $brand,
            'list' => $list,
            'subscriber' => $subscriber,
            'customFields' => $customFields,
        ]);
    }

    /**
     * Update the specified subscriber.
     */
    public function update(Request $request, Brand $brand, EmailList $list, Subscriber $subscriber): RedirectResponse
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);
        abort_if($subscriber->list_id !== $list->id, 404);

        $rules = [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('subscribers')->where(function ($query) use ($list) {
                    return $query->where('list_id', $list->id);
                })->ignore($subscriber->id),
            ],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'custom_fields' => ['nullable', 'array'],
            'status' => ['required', Rule::in([
                Subscriber::STATUS_SUBSCRIBED,
                Subscriber::STATUS_UNSUBSCRIBED,
                Subscriber::STATUS_BOUNCED,
                Subscriber::STATUS_PENDING,
                Subscriber::STATUS_COMPLAINED,
            ])],
        ];

        $validated = $request->validate($rules);

        $oldStatus = $subscriber->status;
        $subscriber->update($validated);

        // Update counts if status changed
        if ($oldStatus !== $validated['status']) {
            $list->updateSubscriberCounts();
        }

        return redirect()
            ->route('brands.lists.show', [$brand, $list])
            ->with('success', 'Subscriber updated successfully.');
    }

    /**
     * Remove the specified subscriber.
     */
    public function destroy(Brand $brand, EmailList $list, Subscriber $subscriber): RedirectResponse
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);
        abort_if($subscriber->list_id !== $list->id, 404);

        $subscriber->delete();
        $list->updateSubscriberCounts();

        return redirect()
            ->route('brands.lists.show', [$brand, $list])
            ->with('success', 'Subscriber deleted successfully.');
    }

    /**
     * Bulk delete subscribers.
     */
    public function bulkDelete(Request $request, Brand $brand, EmailList $list): RedirectResponse
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);

        $request->validate([
            'subscriber_ids' => ['required', 'array', 'min:1'],
            'subscriber_ids.*' => ['required', 'integer', 'exists:subscribers,id'],
        ]);

        $count = Subscriber::where('list_id', $list->id)
            ->whereIn('id', $request->subscriber_ids)
            ->delete();

        $list->updateSubscriberCounts();

        return back()->with('success', "{$count} subscriber(s) deleted successfully.");
    }

    /**
     * Show the CSV import form.
     */
    public function import(Brand $brand, EmailList $list): Response
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);

        $customFields = $list->customFields()->active()->ordered()->get();

        return Inertia::render('Subscribers/Import', [
            'brand' => $brand,
            'list' => $list,
            'customFields' => $customFields,
        ]);
    }

    /**
     * Process CSV import.
     */
    public function processImport(Request $request, Brand $brand, EmailList $list): RedirectResponse
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);

        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'], // 10MB max
            'has_header' => ['boolean'],
        ]);

        $file = $request->file('file');
        $hasHeader = $request->boolean('has_header', true);

        // Store the file
        $path = $file->store('imports', 'local');

        // Create import record
        $import = \App\Models\SubscriberImport::create([
            'list_id' => $list->id,
            'user_id' => $request->user()->id,
            'filename' => $file->getClientOriginalName(),
            'file_path' => $path,
            'status' => \App\Models\SubscriberImport::STATUS_PENDING,
        ]);

        // Dispatch job to process the import
        \App\Jobs\ProcessSubscriberImport::dispatch($import, $hasHeader);

        return redirect()
            ->route('brands.lists.show', [$brand, $list])
            ->with('success', 'Import started! You will see progress updates below.');
    }

    /**
     * Export subscribers to CSV.
     */
    public function export(Brand $brand, EmailList $list)
    {
        $this->authorize('view', $brand);
        abort_if($list->brand_id !== $brand->id, 404);

        $subscribers = Subscriber::where('list_id', $list->id)
            ->orderBy('email')
            ->get();

        $customFields = $list->customFields()->active()->ordered()->get();

        $filename = "subscribers-{$list->id}-" . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($subscribers, $customFields) {
            $file = fopen('php://output', 'w');

            // Header row
            $headerRow = ['Email', 'First Name', 'Last Name', 'Status', 'Subscribed At'];
            foreach ($customFields as $field) {
                $headerRow[] = $field->name;
            }
            fputcsv($file, $headerRow);

            // Data rows
            foreach ($subscribers as $subscriber) {
                $row = [
                    $subscriber->email,
                    $subscriber->first_name,
                    $subscriber->last_name,
                    $subscriber->status,
                    $subscriber->subscribed_at?->format('Y-m-d H:i:s'),
                ];

                foreach ($customFields as $field) {
                    $row[] = $subscriber->custom_fields[$field->tag] ?? '';
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
