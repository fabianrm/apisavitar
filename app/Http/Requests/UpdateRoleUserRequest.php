<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->method();

        if ($method === "PUT") {
            return [
                'user_id' => ['required'],
                'role_id' => ['required'],
                'enterprise_id' => ['required'],
            ];
        } else {
            return [
                'user_id' => ['sometimes', 'required'],
                'role_id' => ['sometimes', 'required'],
                'enterprise_id' => ['sometimes', 'required'],
            ];
        }
    }
}
