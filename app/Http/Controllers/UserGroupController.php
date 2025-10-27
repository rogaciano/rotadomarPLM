<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;

class UserGroupController extends Controller
{
    /**
     * Display a listing of the users with their groups.
     */
    public function index(Request $request)
    {
        $query = User::query();
        
        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $users = $query->paginate(10);
        $groups = Group::all();
        
        return view('user-groups.index', compact('users', 'groups'));
    }
    
    /**
     * Show the form for editing user groups.
     */
    public function edit(User $user)
    {
        $groups = Group::all();
        $userGroups = $user->groups->pluck('id')->toArray();
        
        return view('user-groups.edit', compact('user', 'groups', 'userGroups'));
    }
    
    /**
     * Update the user's groups.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'groups' => 'nullable|array',
            'groups.*' => 'exists:groups,id',
        ]);
        
        $user->groups()->sync($validated['groups'] ?? []);
        
        return redirect()->route('user-groups.index')
            ->with('success', 'Grupos do usuário atualizados com sucesso.');
    }
    
    /**
     * Display the user's permissions.
     */
    public function showPermissions(User $user)
    {
        $userPermissions = collect();
        
        // Get all permissions from user's groups
        foreach ($user->groups as $group) {
            foreach ($group->permissions as $permission) {
                $userPermissions->push($permission);
            }
        }
        
        // Remove duplicates
        $userPermissions = $userPermissions->unique('id');
        
        return view('user-groups.permissions', compact('user', 'userPermissions'));
    }
}
