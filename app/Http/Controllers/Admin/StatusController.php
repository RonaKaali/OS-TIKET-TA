<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Status;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    public function index()
    {
        $items = Status::latest()->paginate(20);
        return view('admin.statuses.index', compact('items'));
    }
    public function create()
    {
        return view('admin.statuses.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:statuses,slug'],
            'is_closing' => ['required', 'boolean'],
        ]);
        Status::create($data);
        return redirect()->route('admin.statuses.index')->with('ok', 'Status dibuat.');
    }

    public function edit(Status $status)
    {
        return view('admin.statuses.edit', compact('status'));
    }

    public function update(Request $r, Status $status)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:statuses,slug,' . $status->id],
            'is_closing' => ['required', 'boolean'],
        ]);
        $status->update($data);
        return redirect()->route('admin.statuses.index')->with('ok', 'Status diperbarui.');
    }

    public function destroy(Status $status)
    {
        $status->delete();
        return back()->with('ok', 'Status dihapus.');
    }
}
