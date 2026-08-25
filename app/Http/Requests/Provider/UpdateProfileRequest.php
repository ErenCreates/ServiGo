<?php

namespace App\Http\Requests\Provider;

use App\Models\Role;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Kullanıcının bu isteği yapmaya yetkili olup olmadığını belirler.
     */
    public function authorize(): bool
    {
        // Kullanıcı giriş yapmış olmalı ve rolü PROVIDER (3) olmalı
        return auth()->check() && (int) auth()->user()->role_id === Role::PROVIDER;
    }

    /**
     * İstek için geçerli olan validasyon kuralları.
     */
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'bio'          => ['required', 'string', 'max:1000'],
            'category_id'  => ['required', 'exists:service_categories,id'],
        ];
    }
}
