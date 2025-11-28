<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Priority;
use Illuminate\Http\Request;

class PriorityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    public function index()
    {
        $items = Priority::orderByDesc('weight')->paginate(20);
        return view('admin.priorities.index', compact('items'));
    }
    public function create()
    {
        return view('admin.priorities.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255', 'unique:prioritas,name'],
            'weight' => ['required', 'integer', 'min:1', 'max:10'],
        ]);
        Priority::create($data);
        return redirect()->route('admin.priorities.index')->with('ok', 'Prioritas dibuat.');
    }

    public function edit(Priority $priority)
    {
        return view('admin.priorities.edit', compact('priority'));
    }

    public function update(Request $r, Priority $priority)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255', 'unique:prioritas,name,' . $priority->id],
            'weight' => ['required', 'integer', 'min:1', 'max:10'],
        ]);
        $priority->update($data);
        return redirect()->route('admin.priorities.index')->with('ok', 'Prioritas diperbarui.');
    }

    public function destroy(Priority $priority)
    {
        $priority->delete();
        return back()->with('ok', 'Prioritas dihapus.');
    }
}
