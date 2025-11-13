<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{HelpTopic, Department};
use Illuminate\Http\Request;

class HelpTopicController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    public function index()
    {
        $items = HelpTopic::with('department')->latest()->paginate(20);
        return view('admin.help-topics.index', compact('items'));
    }
    public function create()
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.help-topics.create', compact('departments'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'form_schema' => ['nullable', 'json'],
        ]);
        HelpTopic::create($data);
        return redirect()->route('admin.help-topics.index')->with('ok', 'Help Topic dibuat.');
    }

    public function edit(HelpTopic $helpTopic)
    {
        $departments = Department::orderBy('name')->get();
        return view('admin.help-topics.edit', compact('helpTopic', 'departments'));
    }

    public function update(Request $r, HelpTopic $helpTopic)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'form_schema' => ['nullable', 'json'],
        ]);
        $helpTopic->update($data);
        return redirect()->route('admin.help-topics.index')->with('ok', 'Help Topic diperbarui.');
    }

    public function destroy(HelpTopic $helpTopic)
    {
        $helpTopic->delete();
        return back()->with('ok', 'Help Topic dihapus.');
    }
}
