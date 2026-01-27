<?php

namespace Modules\PublicPage\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VideoUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'action' => 'required|in:initialize,chunk,finalize,resume,cancel',
        ];

        switch ($this->input('action')) {
            case 'initialize':
                $rules['file'] = 'required|file|max:1048576'; // 1GB max in KB
                break;

            case 'chunk':
                $rules['upload_id'] = 'required|string|uuid';
                $rules['chunk'] = 'required|file|max:5120'; // 5MB max in KB
                $rules['chunk_index'] = 'required|integer|min:0';
                $rules['total_chunks'] = 'required|integer|min:1';
                break;

            case 'finalize':
                $rules['upload_id'] = 'required|string|uuid';
                $rules['title'] = 'required|string|max:255';
                $rules['description'] = 'nullable|string';
                $rules['duration_seconds'] = 'nullable|integer|min:0';
                $rules['is_featured'] = 'boolean';
                $rules['sort_order'] = 'integer|min:0';
                break;

            case 'resume':
                $rules['upload_id'] = 'required|string|uuid';
                break;

            case 'cancel':
                $rules['upload_id'] = 'required|string|uuid';
                break;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'file.required' => 'A video file is required.',
            'file.max' => 'The video file must not exceed 1GB.',
            'chunk.required' => 'A chunk file is required.',
            'chunk.max' => 'The chunk size must not exceed 5MB.',
            'upload_id.required' => 'Upload ID is required.',
            'upload_id.uuid' => 'Invalid upload ID format.',
            'title.required' => 'Video title is required.',
            'action.required' => 'Upload action is required.',
            'action.in' => 'Invalid action. Must be one of: initialize, chunk, finalize, resume, cancel.',
        ];
    }
}
