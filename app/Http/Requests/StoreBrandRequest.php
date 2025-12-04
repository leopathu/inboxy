<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string'],
            'country' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'primary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'monthly_send_limit' => ['nullable', 'integer', 'min:0'],
            'cost_per_email' => ['nullable', 'numeric', 'min:0'],
            'from_name' => ['nullable', 'string', 'max:255'],
            'from_email' => ['nullable', 'email', 'max:255'],
            'reply_to_email' => ['nullable', 'email', 'max:255'],
            'can_create_lists' => ['nullable', 'boolean'],
            'can_create_campaigns' => ['nullable', 'boolean'],
            'can_import_subscribers' => ['nullable', 'boolean'],
            'can_export_data' => ['nullable', 'boolean'],
            'can_view_reports' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
