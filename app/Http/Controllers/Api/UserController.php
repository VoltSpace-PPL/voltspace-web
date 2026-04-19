<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = User::query()->select(['id', 'name', 'email', 'role', 'created_at']);

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        return response()->json($query->latest()->get());
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat.',
            'data' => $user->only(['id', 'name', 'email', 'role', 'created_at']),
        ], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,'.$user->id],
            'role' => ['sometimes', 'in:admin,mahasiswa'],
            'password' => ['sometimes', 'string', 'min:8'],
        ]);

        if (array_key_exists('password', $data)) {
            $data['password'] = Hash::make((string) $data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'User berhasil diupdate.',
            'data' => $user->only(['id', 'name', 'email', 'role', 'created_at', 'updated_at']),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus.',
        ]);
    }
}
