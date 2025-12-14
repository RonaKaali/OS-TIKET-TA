<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Team, User};
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Super Admin']);
    }

    public function index()
    {
        $items = Team::withCount('users')->latest()->paginate(20);
        return view('admin.teams.index', compact('items'));
    }
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('admin.teams.create', compact('users'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255', 'unique:tim,name'],
            'user_ids' => ['array'],
            'user_ids.*' => ['exists:pengguna,id'],
        ]);
        $team = Team::create(['name' => $data['name']]);
        $team->users()->sync($data['user_ids'] ?? []);
        return redirect()->route('admin.teams.index')->with('ok', 'Team dibuat.');
    }

    public function edit(Team $team)
    {
        $users = User::orderBy('name')->get();
        $team->load('users');
        return view('admin.teams.edit', compact('team', 'users'));
    }

    public function update(Request $r, Team $team)
    {
        $data = $r->validate([
            'name' => ['required', 'string', 'max:255', 'unique:tim,name,' . $team->id],
            'user_ids' => ['array'],
            'user_ids.*' => ['exists:pengguna,id'],
        ]);
        $team->update(['name' => $data['name']]);
        $team->users()->sync($data['user_ids'] ?? []);
        return redirect()->route('admin.teams.index')->with('ok', 'Team diperbarui.');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return back()->with('ok', 'Team dihapus.');
    }
}
