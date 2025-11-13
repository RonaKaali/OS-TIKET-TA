<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SlaPlan;
use Illuminate\Http\Request;

class SlaPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    public function index()
    {
        $items = SlaPlan::latest()->paginate(20);
        return view('admin.sla.index', compact('items'));
    }
    public function create()
    {
        return view('admin.sla.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255', 'unique:sla_plans,name'],
            'grace_hours' => ['required', 'integer', 'min:1', 'max:720'],
        ]);
        SlaPlan::create($data);
        return redirect()->route('admin.sla.index')->with('ok', 'SLA dibuat.');
    }

    public function edit(SlaPlan $sla)
    {
        return view('admin.sla.edit', compact('sla'));
    }

    public function update(Request $r, SlaPlan $sla)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255', 'unique:sla_plans,name,' . $sla->id],
            'grace_hours' => ['required', 'integer', 'min:1', 'max:720'],
        ]);
        $sla->update($data);
        return redirect()->route('admin.sla.index')->with('ok', 'SLA diperbarui.');
    }

    public function destroy(SlaPlan $sla)
    {
        $sla->delete();
        return back()->with('ok', 'SLA dihapus.');
    }
}
