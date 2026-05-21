<?php

namespace Modules\PublicPage\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->isAdmin() ?? FALSE);
    }

    public function rules(): array
    {
        return [
            'alt_text' => ['nullable', 'string', 'max:255'],
        ];
    }
}
