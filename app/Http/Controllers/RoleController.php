<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::query()
            ->withCount('users')
            ->withCount('permissions')
            ->when($search = trim((string) $request->query('q')), function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $this->authorize('create', Role::class);

        return view('roles.create', [
            'permissionGroups' => Permission::query()->orderBy('group')->orderBy('label')->get()->groupBy('group'),
            'role' => null,
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $this->authorize('create', Role::class);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'label' => Str::title($request->name),
            'description' => $request->description,
        ]);

        if ($request->filled('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil ditambahkan.');
    }

    public function edit(Role $role): View
    {
        $this->authorize('update', $role);

        $role->load('permissions:id');

        return view('roles.edit', [
            'role' => $role,
            'permissionGroups' => Permission::query()->orderBy('group')->orderBy('label')->get()->groupBy('group'),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $this->authorize('update', $role);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'label' => Str::title($request->name),
            'description' => $request->description,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions ?? []);
        }

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        $this->authorize('delete', $role);

        if ($role->users()->exists()) {
            return redirect()
                ->route('roles.index')
                ->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh pengguna.');
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', 'Role berhasil dihapus.');
    }
}