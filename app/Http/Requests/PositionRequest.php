<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class PositionRequest extends FormRequest
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
            'description' => 'required',
            'company_id' => 'required',
            'salary' => 'nullable',
            'work_schedule' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => "Текст сообщения: $this->required",
            'description.required' => $this->required,
            'company_id.required' => $this->required,
        ];
    }
}
