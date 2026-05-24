<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isEmployee();
    }

    public function rules(): array
    {
        return [
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
