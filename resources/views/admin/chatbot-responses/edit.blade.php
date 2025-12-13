<x-admin-layout>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Chatbot Response</h1>
                <p class="text-sm text-gray-600 mt-1">Ubah response chatbot yang sudah ada</p>
            </div>
            <a href="{{ route('admin.chatbot-responses.index') }}"
                class="text-gray-600 hover:text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('admin.chatbot-responses.update', $chatbotResponse) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="keyword" class="block text-sm font-medium text-gray-700 mb-2">
                    Keyword / Pertanyaan * 
                    <span class="text-xs text-gray-500 font-normal">(Kata kunci yang akan dicocokkan dengan pesan user)</span>
                </label>
                <input type="text" name="keyword" id="keyword" value="{{ old('keyword', $chatbotResponse->keyword) }}" required
                    placeholder="Contoh: halo, selamat pagi, info"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500">
                    💡 Tips: Gunakan kata kunci yang umum digunakan user saat bertanya
                </p>
                @error('keyword')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="response" class="block text-sm font-medium text-gray-700 mb-2">
                    Response / Jawaban * 
                    <span class="text-xs text-gray-500 font-normal">(Pesan yang akan dikirim bot)</span>
                </label>
                <textarea name="response" id="response" rows="10" required
                    placeholder="Tuliskan response yang akan dikirim bot kepada user..."
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('response', $chatbotResponse->response) }}</textarea>
                <p class="mt-1 text-xs text-gray-500">
                    💡 Tips: Buat response yang jelas, ramah, dan informatif. Gunakan <code>\n</code> untuk baris baru. Format HTML tidak didukung, gunakan plain text.
                </p>
                @error('response')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="match_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Pencocokan *
                    </label>
                    <select name="match_type" id="match_type" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="contains" {{ old('match_type', $chatbotResponse->match_type) === 'contains' ? 'selected' : '' }}>
                            Contains (Mengandung kata)
                        </option>
                        <option value="exact" {{ old('match_type', $chatbotResponse->match_type) === 'exact' ? 'selected' : '' }}>
                            Exact (Persis sama)
                        </option>
                        <option value="starts_with" {{ old('match_type', $chatbotResponse->match_type) === 'starts_with' ? 'selected' : '' }}>
                            Starts With (Dimulai dengan)
                        </option>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        💡 <strong>Contains:</strong> Cocok jika pesan mengandung keyword<br>
                        💡 <strong>Exact:</strong> Cocok jika pesan persis sama dengan keyword<br>
                        💡 <strong>Starts With:</strong> Cocok jika pesan dimulai dengan keyword
                    </p>
                    @error('match_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">
                        Priority *
                        <span class="text-xs text-gray-500 font-normal">(0-100, lebih tinggi = lebih prioritas)</span>
                    </label>
                    <input type="number" name="priority" id="priority" value="{{ old('priority', $chatbotResponse->priority) }}" 
                        min="0" max="100" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">
                        💡 Jika ada beberapa response dengan keyword yang sama, yang priority lebih tinggi akan dipilih
                    </p>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" 
                        {{ old('is_active', $chatbotResponse->is_active) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <span class="ml-2 text-sm text-gray-700">Aktifkan response ini</span>
                </label>
                <p class="mt-1 text-xs text-gray-500">
                    💡 Response yang tidak aktif tidak akan digunakan oleh bot
                </p>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('admin.chatbot-responses.index') }}"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Update Response
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>

