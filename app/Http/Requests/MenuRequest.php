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
            'category' => ($isUpdate ? 'sometimes' : 'required') . '|in:makanan,minuman',
            'status' => ($isUpdate ? 'sometimes' : 'required') . '|in:tersedia,tidak-tersedia',
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
            'name.required' => 'Nama menu wajib diisi.',
            'name.string' => 'Nama menu harus berupa string.',
            'name.unique' => 'Nama menu sudah ada.',
            'name.max' => 'Nama menu tidak boleh lebih dari 255 karakter.',

            'description.string' => 'Deskripsi harus berupa string.',

            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh kurang dari 0.',

            'category.required' => 'Kategori wajib diisi.',
            'category.in' => 'Kategori harus berupa makanan atau minuman.',

            'status.required' => 'Status wajib diisi.',
            'status.in' => 'Status harus berupa tersedia atau tidak tersedia.',

            'image.image' => 'Gambar harus berupa file gambar yang valid.',
            'image.max' => 'Gambar tidak boleh lebih dari 2048 kilobyte.',
        ];
    }
}
