<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\AdminActivityLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;

class AdminActivityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = AdminActivityLog::with('user:id,nama,email,role,avatar_url')
            ->orderByDesc('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->string('action'));
        }

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->string('dari'));
        }

        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->string('sampai'));
        }

        if ($request->filled('search')) {
            $q = $request->string('search');
            $query->where(function ($sub) use ($q) {
                $sub->where('description', 'like', "%{$q}%")
                    ->orWhere('action', 'like', "%{$q}%");
            });
        }

        $page    = max(1, (int) $request->get('page', 1));
        $perPage = min(100, max(10, (int) $request->get('per_page', 30)));

        $paginated = $query->paginate($perPage, ['*'], 'page', $page);

        // Distinct action list for filter dropdown
        $actions = AdminActivityLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Admin users for filter dropdown
        $admins = User::whereIn('role', User::ADMIN_ROLES)
            ->select('id', 'nama', 'email', 'role')
            ->orderBy('nama')
            ->get();

        return Response::json([
            'success' => true,
            'data'    => $paginated->items(),
            'meta'    => [
                'total'        => $paginated->total(),
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
            ],
            'actions' => $actions,
            'admins'  => $admins,
        ]);
    }

    public function updateRole(Request $request, int $userId): JsonResponse
    {
        $actor = auth()->user();

        // Only admin and super_admin can change roles
        if (!$actor->isSuperAdmin()) {
            return Response::json(['success' => false, 'message' => 'Akses tidak diizinkan'], 403);
        }

        $newRole = $request->string('role')->toString();

        if (!in_array($newRole, User::ROLES, true)) {
            return Response::json(['success' => false, 'message' => 'Role tidak valid'], 422);
        }

        $user = User::findOrFail($userId);

        if ($user->id === $actor->id) {
            return Response::json(['success' => false, 'message' => 'Tidak dapat mengubah role sendiri'], 422);
        }

        // Hierarchy: admin can only manage roles strictly below admin
        if ($actor->role === 'admin') {
            $actorIdx  = array_search('admin', User::ROLES, true);
            $targetIdx = array_search($user->role, User::ROLES, true);
            $newIdx    = array_search($newRole, User::ROLES, true);

            if ($targetIdx >= $actorIdx) {
                return Response::json(['success' => false, 'message' => 'Tidak dapat mengubah role pengguna setingkat atau lebih tinggi'], 403);
            }
            if ($newIdx >= $actorIdx) {
                return Response::json(['success' => false, 'message' => 'Tidak dapat memberikan role setingkat atau lebih tinggi dari Admin'], 403);
            }
        }
        // super_admin has no restriction

        $oldRole = $user->role;
        $user->update(['role' => $newRole]);

        AdminActivityLog::record(
            'update_role',
            "Mengubah role {$user->email} dari {$oldRole} ke {$newRole}",
            User::class,
            $user->id,
            ['old_role' => $oldRole, 'new_role' => $newRole]
        );

        return Response::json(['success' => true, 'message' => 'Role berhasil diperbarui']);
    }
}
