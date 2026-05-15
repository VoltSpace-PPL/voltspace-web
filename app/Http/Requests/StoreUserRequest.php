<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
     * @return array<string, string>
     */
    public function rules(): array
    {
        $actor = auth()->user();
        $roles = $actor && $actor->isSuperAdmin()
            ? 'admin,mahasiswa,super_admin'
            : 'mahasiswa';

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:'.$roles,
            'password' => 'required|string|min:8',
        ];
    }
}
