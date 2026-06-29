<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Organization};
use App\Traits\LoggableActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use LoggableActivity;
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $r)
    {
        $q = User::query()->with('roles');

        // Filter berdasarkan role
        if ($r->filled('role')) {
            $q->whereHas('roles', function ($query) use ($r) {
                $query->where('name', $r->role);
            });
        }

        // Search
        if ($r->filled('search')) {
            $search = $r->search;
            $q->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $q->with('organization')->latest()->paginate(20)->withQueryString();
        $roles = Role::all();

        // Log aktivitas READ (list)
        $this->logRead('User', null, ['action' => 'list', 'filters' => $r->only(['role', 'search'])]);

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $organizations = Organization::orderBy('name')->get();
        return view('admin.users.create', compact('roles', 'organizations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:pengguna,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'organization_id' => ['nullable', 'exists:organisasi,id'],
            'role' => ['required', 'exists:roles,name'],
            'allow_after_hours_access' => ['sometimes', 'boolean'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'telepon' => $data['phone'] ?? null,
            'id_organisasi' => $data['organization_id'] ?? null,
            'allow_after_hours_access' => $r->boolean('allow_after_hours_access'),
        ]);

        // Assign role
        $user->assignRole($data['role']);

        // Log aktivitas CREATE
        $this->logCreate('User', $user, ['role' => $data['role']]);

        return redirect()->route('admin.users.index')->with('ok', 'User berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $organizations = Organization::orderBy('name')->get();
        $user->load('roles', 'organization');
        
        // Log aktivitas READ (view edit form)
        $this->logRead('User', $user, ['action' => 'edit_form']);
        
        return view('admin.users.edit', compact('user', 'roles', 'organizations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $r, User $user)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:pengguna,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'organization_id' => ['nullable', 'exists:organisasi,id'],
            'role' => ['required', 'exists:roles,name'],
            'allow_after_hours_access' => ['sometimes', 'boolean'],
        ]);

        // Simpan data original untuk logging
        $originalData = [
            'name' => $user->name,
            'email' => $user->email,
            'telepon' => $user->telepon,
            'id_organisasi' => $user->id_organisasi,
            'allow_after_hours_access' => (bool) $user->allow_after_hours_access,
        ];

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'telepon' => $data['phone'] ?? null,
            'id_organisasi' => $data['organization_id'] ?? null,
            'allow_after_hours_access' => $r->boolean('allow_after_hours_access'),
        ];

        // Update password jika diisi
        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $user->update($updateData);

        // Sync role
        $oldRole = $user->roles->first()?->name;
        $user->syncRoles([$data['role']]);

        // Log aktivitas UPDATE
        $this->logUpdate('User', $user, $originalData, [
            'role_changed' => $oldRole !== $data['role'],
            'old_role' => $oldRole,
            'new_role' => $data['role'],
        ]);

        return redirect()->route('admin.users.index')->with('ok', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Tidak dapat menghapus akun sendiri.']);
        }

        // Log aktivitas DELETE (sebelum dihapus)
        $this->logDelete('User', $user);

        $user->delete();

        return back()->with('ok', 'User berhasil dihapus.');
    }
}
