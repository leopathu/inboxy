<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreListRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'description' => ['nullable', 'string'],
            'from_name' => ['nullable', 'string', 'max:255'],
            'from_email' => ['nullable', 'email', 'max:255'],
            'reply_to_email' => ['nullable', 'email', 'max:255'],
            'subscribe_success_message' => ['nullable', 'string'],
            'unsubscribe_success_message' => ['nullable', 'string'],
            'require_confirmation' => ['nullable', 'boolean'],
            'confirmation_email_subject' => ['nullable', 'string', 'max:255'],
            'confirmation_email_body' => ['nullable', 'string'],
            'send_welcome_email' => ['nullable', 'boolean'],
            'welcome_email_subject' => ['nullable', 'string', 'max:255'],
            'welcome_email_body' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
