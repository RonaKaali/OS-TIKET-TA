<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CannedResponse;
use Illuminate\Http\Request;

class CannedResponseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    public function index()
    {
        $items = CannedResponse::latest()->paginate(20);
        return view('admin.canned.index', compact('items'));
    }
    public function create()
    {
        return view('admin.canned.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:50000'],
        ]);
        CannedResponse::create($data);
        return redirect()->route('admin.canned.index')->with('ok', 'Template dibuat.');
    }

    public function edit(CannedResponse $canned)
    {
        return view('admin.canned.edit', ['cannedResponse' => $canned]);
    }

    public function update(Request $r, CannedResponse $canned)
    {
        $data = $r->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:50000'],
        ]);
        $canned->update($data);
        return redirect()->route('admin.canned.index')->with('ok', 'Template diperbarui.');
    }

    public function destroy(CannedResponse $canned)
    {
        $canned->delete();
        return back()->with('ok', 'Template dihapus.');
    }
}
