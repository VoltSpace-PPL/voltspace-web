<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json($request->user()->only([
            'id', 'name', 'email', 'role', 'created_at', 'updated_at',
        ]));
    }

    public function update(Request $request): JsonResponse
    {
        $u = $request->user();
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,'.$u->id],
            'password' => ['sometimes', 'string', 'min:8'],
        ]);

        if (array_key_exists('password', $data)) {
            $data['password'] = Hash::make((string) $data['password']);
        }

        $u->update($data);

        return response()->json([
            'message' => 'Profil diperbarui.',
            'data' => $u->fresh()->only([
                'id', 'name', 'email', 'role', 'created_at', 'updated_at',
            ]),
        ]);
    }
}
