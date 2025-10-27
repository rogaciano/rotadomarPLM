<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    /**
     * Show the form to edit granular permissions for a specific user.
     */
    public function edit(User $user)
    {
        $permissions = Permission::orderBy('name')->get();
        $userPermissions = $user->userPermissions()->get()->keyBy('permission_id');

        return view('user-permissions.edit', compact('user', 'permissions', 'userPermissions'));
    }

    /**
     * Update granular permissions for a specific user.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->input('permissions', []);

        // Recupera todas as permissões existentes para evitar queries N+1
        $allPermissions = Permission::pluck('id');

        foreach ($allPermissions as $permissionId) {
            $flags = $data[$permissionId] ?? [];

            $canCreate = !empty($flags['can_create']);
            $canRead   = !empty($flags['can_read']);
            $canUpdate = !empty($flags['can_update']);
            $canDelete = !empty($flags['can_delete']);

            $hasAny = $canCreate || $canRead || $canUpdate || $canDelete;

            if ($hasAny) {
                UserPermission::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'permission_id' => $permissionId,
                    ],
                    [
                        'can_create' => $canCreate,
                        'can_read'   => $canRead,
                        'can_update' => $canUpdate,
                        'can_delete' => $canDelete,
                    ]
                );
            } else {
                // Se todas as flags estiverem desmarcadas, remove a configuração específica
                UserPermission::where('user_id', $user->id)
                    ->where('permission_id', $permissionId)
                    ->delete();
            }
        }

        return redirect()
            ->route('user-permissions.edit', $user->id)
            ->with('success', 'Permissões específicas do usuário atualizadas com sucesso.');
    }
}
