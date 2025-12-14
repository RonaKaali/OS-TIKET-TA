<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Organization};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
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
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'telepon' => $data['phone'] ?? null,
            'id_organisasi' => $data['organization_id'] ?? null,
        ]);

        // Assign role
        $user->assignRole($data['role']);

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
        ]);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'telepon' => $data['phone'] ?? null,
            'id_organisasi' => $data['organization_id'] ?? null,
        ];

        // Update password jika diisi
        if (!empty($data['password'])) {
            $updateData['password'] = $data['password'];
        }

        $user->update($updateData);

        // Sync role
        $user->syncRoles([$data['role']]);

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

        $user->delete();

        return back()->with('ok', 'User berhasil dihapus.');
    }
}
