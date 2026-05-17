<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $actor = $request->user();
        $query = User::query()->select([
            'id', 'name', 'email', 'role', 'created_at',
            'energy_alert_high_usage_kwh', 'energy_alert_peak_kw',
        ]);

        if ($actor->isSuperAdmin()) {
            if ($request->filled('role')) {
                $query->where('role', $request->string('role'));
            }
        } elseif ($actor->isStaffAdmin()) {
            $query->where(function ($q) use ($actor): void {
                $q->where('id', $actor->id)
                    ->orWhere('role', 'mahasiswa');
            });
        } else {
            $query->where('id', $actor->id);
        }

        return response()->json($query->latest()->get());
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $actor = $request->user();
        if (! $actor->isStaffAdmin()) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $role = $request->input('role');
        if ($actor->isAdmin() && $role !== 'mahasiswa') {
            return response()->json(['message' => 'Admin hanya dapat menambahkan mahasiswa.'], 422);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'role' => $role,
            'password' => Hash::make($request->input('password')),
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat.',
            'data' => $user->only(['id', 'name', 'email', 'role', 'created_at']),
        ], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $actor = $request->user();
        if (! $this->canManageUser($actor, $user, 'update')) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $roleRule = Rule::in($actor->isSuperAdmin() ? ['admin', 'mahasiswa', 'super_admin'] : ['mahasiswa']);
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,'.$user->id],
            'role' => ['sometimes', $roleRule],
            'password' => ['sometimes', 'string', 'min:8'],
            'energy_alert_high_usage_kwh' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'energy_alert_peak_kw' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ]);

        if ($actor->isAdmin()) {
            unset($data['role']);
        }

        if ($actor->isMahasiswa() && $user->id === $actor->id) {
            unset($data['role']);
        }

        if (array_key_exists('password', $data)) {
            $data['password'] = Hash::make((string) $data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'User berhasil diupdate.',
            'data' => $user->fresh()->only([
                'id', 'name', 'email', 'role', 'created_at', 'updated_at',
                'energy_alert_high_usage_kwh', 'energy_alert_peak_kw',
            ]),
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $actor = $request->user();
        if (! $actor->isSuperAdmin()) {
            return response()->json(['message' => 'Hanya super admin yang dapat menghapus pengguna.'], 403);
        }
        if ($user->id === $actor->id) {
            return response()->json(['message' => 'Tidak dapat menghapus akun sendiri.'], 422);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus.',
        ]);
    }

    private function canManageUser(User $actor, User $target, string $action): bool
    {
        if ($actor->isSuperAdmin()) {
            return true;
        }
        if ($actor->isAdmin()) {
            if ($target->id === $actor->id) {
                return true;
            }

            return $target->isMahasiswa();
        }
        if ($actor->isMahasiswa()) {
            return $target->id === $actor->id;
        }

        return false;
    }
}
