<?php

namespace Modules\PublicPage\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventPhotosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) ($this->user()?->isAdmin() ?? FALSE);
    }

    public function rules(): array
    {
        return [
            'photos' => ['required', 'array', 'min:1', 'max:20'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'alt_texts' => ['nullable', 'array'],
            'alt_texts.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'photos.max' => 'You can upload up to 20 photos at once.',
            'photos.*.max' => 'Each photo must be 10 MB or smaller.',
            'photos.*.image' => 'Each file must be an image (jpeg, png, jpg, gif, or webp).',
        ];
    }
}
