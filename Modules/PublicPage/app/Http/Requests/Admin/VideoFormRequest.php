<?php

namespace Modules\PublicPage\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VideoFormRequest extends FormRequest
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
      'title' => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'video_url' => [$this->isMethod('PUT') ? 'exclude' : 'required', 'url'],
      'thumbnail_url' => [$this->isMethod('PUT') ? 'exclude' : 'nullable', 'url'],
      'duration_seconds' => ['nullable', 'integer', 'min:0'],
      'is_featured' => ['boolean'],
      'sort_order' => ['integer', 'min:0'],
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
      'title.required' => 'Video title is required.',
      'title.max' => 'Video title must not exceed 255 characters.',
      'video_url.required' => 'Video URL is required.',
      'video_url.url' => 'Video URL must be a valid URL.',
      'thumbnail_url.url' => 'Thumbnail URL must be a valid URL.',
      'duration_seconds.integer' => 'Duration must be a whole number of seconds.',
      'duration_seconds.min' => 'Duration cannot be negative.',
      'sort_order.integer' => 'Sort order must be a whole number.',
      'sort_order.min' => 'Sort order cannot be negative.',
    ];
  }
}
