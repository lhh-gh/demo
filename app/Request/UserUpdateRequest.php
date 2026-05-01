<?php

declare(strict_types=1);

namespace App\Request;

use Hyperf\Validation\Request\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = (int) $this->route('id');

        return [
            'name' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:users,email,' . $id,
            'age' => 'required|integer|min:0|max:150',
        ];
    }
}