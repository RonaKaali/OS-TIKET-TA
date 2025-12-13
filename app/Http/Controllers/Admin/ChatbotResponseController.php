<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatbotResponse;
use Illuminate\Http\Request;

class ChatbotResponseController extends Controller
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
        $q = ChatbotResponse::query();

        // Filter berdasarkan status
        if ($r->filled('status')) {
            $q->where('is_active', $r->status === 'active');
        }

        // Search
        if ($r->filled('search')) {
            $search = $r->search;
            $q->where(function ($query) use ($search) {
                $query->where('keyword', 'like', "%{$search}%")
                    ->orWhere('response', 'like', "%{$search}%");
            });
        }

        $items = $q->ordered()->paginate(20)->withQueryString();
        return view('admin.chatbot-responses.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.chatbot-responses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'keyword' => ['required', 'string', 'max:255'],
            'response' => ['required', 'string'],
            'is_active' => ['required', 'boolean'],
            'priority' => ['required', 'integer', 'min:0', 'max:100'],
            'match_type' => ['required', 'in:contains,exact,starts_with'],
        ]);

        ChatbotResponse::create($data);
        return redirect()->route('admin.chatbot-responses.index')
            ->with('ok', 'Response chatbot berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ChatbotResponse $chatbotResponse)
    {
        return view('admin.chatbot-responses.show', compact('chatbotResponse'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ChatbotResponse $chatbotResponse)
    {
        return view('admin.chatbot-responses.edit', compact('chatbotResponse'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $r, ChatbotResponse $chatbotResponse)
    {
        $data = $r->validate([
            'keyword' => ['required', 'string', 'max:255'],
            'response' => ['required', 'string'],
            'is_active' => ['required', 'boolean'],
            'priority' => ['required', 'integer', 'min:0', 'max:100'],
            'match_type' => ['required', 'in:contains,exact,starts_with'],
        ]);

        $chatbotResponse->update($data);
        return redirect()->route('admin.chatbot-responses.index')
            ->with('ok', 'Response chatbot berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChatbotResponse $chatbotResponse)
    {
        $keyword = $chatbotResponse->keyword;
        $chatbotResponse->delete();
        return redirect()->route('admin.chatbot-responses.index')
            ->with('ok', "Response chatbot dengan keyword '{$keyword}' berhasil dihapus.");
    }
}
