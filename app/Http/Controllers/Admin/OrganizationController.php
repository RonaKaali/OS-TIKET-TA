<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    public function index()
    {
        $items = Organization::latest()->paginate(20);
        return view('admin.organizations.index', compact('items'));
    }

    public function create()
    {
        return view('admin.organizations.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255', 'unique:organisasi,name'],
        ]);
        Organization::create($data);
        return redirect()->route('admin.organizations.index')->with('ok', 'Organisasi dibuat.');
    }

    public function edit(Organization $organization)
    {
        return view('admin.organizations.edit', compact('organization'));
    }

    public function update(Request $r, Organization $organization)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255', 'unique:organisasi,name,' . $organization->id],
        ]);
        $organization->update($data);
        return redirect()->route('admin.organizations.index')->with('ok', 'Organisasi diperbarui.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();
        return back()->with('ok', 'Organisasi dihapus.');
    }
}
