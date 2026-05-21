<?php

namespace Modules\PublicPage\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VideoThumbnailRequest extends FormRequest
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
      'thumbnail' => ['required', 'image', 'max:5120'],
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
      'thumbnail.required' => 'Please select an image file.',
      'thumbnail.image' => 'The file must be an image.',
      'thumbnail.max' => 'The image may not be larger than 5MB.',
    ];
  }
}
