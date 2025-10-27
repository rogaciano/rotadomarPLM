<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Permission;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Group::query();
        
        // Apply search filter if provided
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $groups = $query->paginate(10);
        
        return view('groups.index', compact('groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        $modules = Permission::distinct('module')->pluck('module');
        
        return view('groups.create', compact('permissions', 'modules'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        $group = Group::create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);
        
        if (isset($validated['permissions'])) {
            $group->permissions()->attach($validated['permissions']);
        }
        
        return redirect()->route('groups.index')
            ->with('success', 'Grupo criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Group $group)
    {
        return view('groups.show', compact('group'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Group $group)
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        $modules = Permission::distinct('module')->pluck('module');
        $groupPermissions = $group->permissions->pluck('id')->toArray();
        
        return view('groups.edit', compact('group', 'permissions', 'modules', 'groupPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $group->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
        
        $group->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);
        
        $group->permissions()->sync($validated['permissions'] ?? []);
        
        return redirect()->route('groups.index')
            ->with('success', 'Grupo atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Group $group)
    {
        $group->delete();
        
        return redirect()->route('groups.index')
            ->with('success', 'Grupo excluído com sucesso.');
    }
    
    /**
     * Restore a soft-deleted group.
     */
    public function restore($id)
    {
        $group = Group::withTrashed()->findOrFail($id);
        $group->restore();
        
        return redirect()->route('groups.index')
            ->with('success', 'Grupo restaurado com sucesso.');
    }
    
    /**
     * Permanently delete a group.
     */
    public function forceDelete($id)
    {
        $group = Group::withTrashed()->findOrFail($id);
        $group->permissions()->detach();
        $group->users()->detach();
        $group->forceDelete();
        
        return redirect()->route('groups.index')
            ->with('success', 'Grupo excluído permanentemente.');
    }
}
