<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class CompanyRequest extends FormRequest
{
    private string $required = 'обязательное поле для заполнения';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'description' => 'required|string|min:3|max:1500'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Название сообщения: $this->required",
            'description.required' => "Описание: $this->required",
            'description.max:1500' => "Длина описании не должно привышать 1500 букв",
        ];
    }
}
