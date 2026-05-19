<?php

namespace Modules\PublicPage\Http\Requests\Admin;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class EventFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Authorization handled by IsAdmin middleware.
     */
    public function authorize(): bool
    {
        return TRUE;
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
            'icon' => ['nullable', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(config('event_categories'))],
            'event_date' => ['required', 'date'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'is_published' => ['boolean'],
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Event name is required.',
            'name.max' => 'Event name must not exceed 255 characters.',
            'category.required' => 'Event category is required.',
            'category.max' => 'Event category must not exceed 100 characters.',
            'event_date.required' => 'Event date is required.',
            'event_date.date' => 'Event date must be a valid date.',
            'icon.max' => 'Event icon must not exceed 255 characters.',
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and hyphens. No consecutive hyphens allowed.',
            'slug.max' => 'Slug must not exceed 255 characters.',
        ];
    }
}
