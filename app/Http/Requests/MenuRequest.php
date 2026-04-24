<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        return [
            'name' => ($isUpdate ? 'sometimes' : 'required') . '|string|unique:menus,name|max:255',
            'description' => 'nullable|string',
            'price' => ($isUpdate ? 'sometimes' : 'required') . '|numeric|min:0',
            'category' => ($isUpdate ? 'sometimes' : 'required') . '|in:food,drink',
            'status' => ($isUpdate ? 'sometimes' : 'required') . '|in:available,unavailable',
            'image' => 'sometimes|nullable|image|max:2048',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than 255 characters.',

            'description.string' => 'The description must be a string.',

            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price must be a number.',
            'price.min' => 'The price must be at least 0.',

            'category.required' => 'The category field is required.',
            'category.in' => 'The category must be either food or drink.',

            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be either available or unavailable.',

            'image.image' => 'The image must be a valid image file.',
            'image.max' => 'The image may not be greater than 2048 kilobytes.',
        ];
    }
}
