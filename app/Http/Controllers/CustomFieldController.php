<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\CustomField;
use App\Models\EmailList;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CustomFieldController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of custom fields for a list.
     */
    public function index(Brand $brand, EmailList $list): Response
    {
        $this->authorize('view', $brand);
        abort_if($list->brand_id !== $brand->id, 404);

        $customFields = CustomField::where('list_id', $list->id)
            ->ordered()
            ->get();

        return Inertia::render('CustomFields/Index', [
            'brand' => $brand,
            'list' => $list,
            'customFields' => $customFields,
            'fieldTypes' => CustomField::getTypes(),
        ]);
    }

    /**
     * Show the form for creating a new custom field.
     */
    public function create(Brand $brand, EmailList $list): Response
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);

        return Inertia::render('CustomFields/Create', [
            'brand' => $brand,
            'list' => $list,
            'fieldTypes' => CustomField::getTypes(),
        ]);
    }

    /**
     * Store a newly created custom field.
     */
    public function store(Request $request, Brand $brand, EmailList $list): RedirectResponse
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tag' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Z0-9_]+$/',
                Rule::unique('custom_fields')->where(function ($query) use ($list) {
                    return $query->where('list_id', $list->id);
                }),
            ],
            'type' => ['required', Rule::in(array_keys(CustomField::getTypes()))],
            'options' => ['nullable', 'array'],
            'is_required' => ['boolean'],
            'default_value' => ['nullable', 'string'],
            'help_text' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);

        // Auto-calculate sort order if not provided
        if (!isset($validated['sort_order'])) {
            $maxSort = CustomField::where('list_id', $list->id)->max('sort_order') ?? 0;
            $validated['sort_order'] = $maxSort + 1;
        }

        CustomField::create([
            'list_id' => $list->id,
            ...$validated,
        ]);

        return redirect()
            ->route('brands.lists.custom-fields.index', [$brand, $list])
            ->with('success', 'Custom field created successfully.');
    }

    /**
     * Show the form for editing the specified custom field.
     */
    public function edit(Brand $brand, EmailList $list, CustomField $customField): Response
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);
        abort_if($customField->list_id !== $list->id, 404);

        return Inertia::render('CustomFields/Edit', [
            'brand' => $brand,
            'list' => $list,
            'customField' => $customField,
            'fieldTypes' => CustomField::getTypes(),
        ]);
    }

    /**
     * Update the specified custom field.
     */
    public function update(Request $request, Brand $brand, EmailList $list, CustomField $customField): RedirectResponse
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);
        abort_if($customField->list_id !== $list->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'tag' => [
                'required',
                'string',
                'max:255',
                'regex:/^[A-Z0-9_]+$/',
                Rule::unique('custom_fields')->where(function ($query) use ($list) {
                    return $query->where('list_id', $list->id);
                })->ignore($customField->id),
            ],
            'type' => ['required', Rule::in(array_keys(CustomField::getTypes()))],
            'options' => ['nullable', 'array'],
            'is_required' => ['boolean'],
            'default_value' => ['nullable', 'string'],
            'help_text' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);

        $customField->update($validated);

        return redirect()
            ->route('brands.lists.custom-fields.index', [$brand, $list])
            ->with('success', 'Custom field updated successfully.');
    }

    /**
     * Remove the specified custom field.
     */
    public function destroy(Brand $brand, EmailList $list, CustomField $customField): RedirectResponse
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);
        abort_if($customField->list_id !== $list->id, 404);

        $customField->delete();

        return redirect()
            ->route('brands.lists.custom-fields.index', [$brand, $list])
            ->with('success', 'Custom field deleted successfully.');
    }

    /**
     * Reorder custom fields.
     */
    public function reorder(Request $request, Brand $brand, EmailList $list): RedirectResponse
    {
        $this->authorize('update', $brand);
        abort_if($list->brand_id !== $brand->id, 404);

        $validated = $request->validate([
            'field_ids' => ['required', 'array'],
            'field_ids.*' => ['integer', 'exists:custom_fields,id'],
        ]);

        foreach ($validated['field_ids'] as $index => $fieldId) {
            CustomField::where('id', $fieldId)
                ->where('list_id', $list->id)
                ->update(['sort_order' => $index + 1]);
        }

        return redirect()
            ->route('brands.lists.custom-fields.index', [$brand, $list])
            ->with('success', 'Fields reordered successfully.');
    }
}
