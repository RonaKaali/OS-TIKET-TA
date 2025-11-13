<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    public function index()
    {
        $items = Department::latest()->paginate(20);
        return view('admin.departments.index', compact('items'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departemen,nama'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_public' => ['required', 'boolean'],
        ]);
        Department::create([
            'nama' => $data['name'],
            'email' => $data['email'],
            'publik' => $data['is_public'],
        ]);
        return redirect()->route('admin.departments.index')->with('ok', 'Department dibuat.');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $r, Department $department)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departemen,nama,' . $department->id],
            'email' => ['nullable', 'email', 'max:255'],
            'is_public' => ['required', 'boolean'],
        ]);
        $department->update([
            'nama' => $data['name'],
            'email' => $data['email'],
            'publik' => $data['is_public'],
        ]);
        return redirect()->route('admin.departments.index')->with('ok', 'Department diperbarui.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return back()->with('ok', 'Department dihapus.');
    }
}
