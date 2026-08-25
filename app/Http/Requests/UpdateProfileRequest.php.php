<?php

namespace App\Http\Requests\Provider;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
   public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'bio'          => ['required', 'string', 'max:2000'],
            'category_id'  => ['required', 'exists:service_categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Şirket adı zorunludur.',
            'bio.required'           => 'Biyografi alanı zorunludur.',
            'category_id.required'  => 'Lütfen bir hizmet kategorisi seçin.',
            'category_id.exists'    => 'Seçilen kategori geçerli değil.',
        ];
    }
}