<style>
    [x-cloak] { display: none !important; }

    #chatbot-widget {
        position: fixed !important;
        bottom: 24px !important;
        right: 24px !important;
        z-index: 99999 !important;
    }

    #chatbot-button {
        width: 64px !important;
        height: 64px !important;
    }

    @media (max-width: 640px) {
        #chatbot-widget {
            bottom: 16px !important;
            right: 16px !important;
        }
        #chatbot-panel {
            width: calc(100vw - 32px) !important;
            max-width: calc(100vw - 32px) !important;
        }
    }

    @keyframes pulse-custom {
        0%, 100% { transform: scale(1); box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }
        50% { transform: scale(1.05); box-shadow: 0 0 30px rgba(16, 185, 129, 0.5); }
    }

    .animate-pulse-custom { animation: pulse-custom 2s infinite ease-in-out; }

    .chatbot-chip {
        white-space: nowrap;
        flex-shrink: 0;
    }

    .chatbot-msg ul {
        list-style: disc;
        padding-left: 1.1rem;
        margin: 0.35rem 0;
    }

    .chatbot-msg li { margin: 0.15rem 0; }
</style>

@php
    $quickTopics = config('chatbot.quick_topics', []);
    $botName = config('chatbot.name', 'Asisten CSIRT');
@endphp

<div id="chatbot-widget"
     data-chatbot-url="{{ route('chatbot.message') }}"
     x-data="chatbotData()"
     class="fixed bottom-6 right-6 z-[99999]">

    <!-- Floating Button -->
    <button id="chatbot-button"
            @click="toggleChat()"
            :class="{'animate-pulse-custom': !isOpen}"
            class="bg-emerald-600 hover:bg-emerald-500 text-white rounded-full w-16 h-16 shadow-lg shadow-emerald-500/30 hover:shadow-emerald-500/50 transition-all transform hover:scale-105 flex items-center justify-center"
            aria-label="Buka chat bantuan">
        <svg x-show="!isOpen" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <svg x-show="isOpen" x-cloak class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Chat Panel -->
    <div id="chatbot-panel"
         x-show="isOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="absolute bottom-20 right-0 w-[22rem] sm:w-96 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 flex flex-col overflow-hidden"
         style="height: 32rem; max-height: calc(100vh - 120px);">

        <!-- Header -->
        <div class="bg-gradient-to-r from-emerald-600 to-teal-600 p-4 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center relative">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-300 border-2 border-emerald-600 rounded-full"></span>
                </div>
                <div>
                    <div class="font-bold text-white text-sm">{{ $botName }}</div>
                    <div class="text-[10px] text-emerald-100 font-medium">Bantuan pelaporan & solusi insiden</div>
                </div>
            </div>
            <button @click="toggleChat()" class="text-white/80 hover:text-white p-1" aria-label="Tutup chat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Messages -->
        <div id="chatbot-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50 dark:bg-slate-950/50">
            <div class="flex items-start gap-2">
                <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-500/20 rounded-full flex items-center justify-center shrink-0">
                    <span class="text-emerald-700 dark:text-emerald-300 text-[10px] font-black">CS</span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl rounded-tl-sm p-3 shadow-sm">
                        <div class="chatbot-msg text-sm text-slate-700 dark:text-slate-200 leading-relaxed">
                            Halo! Saya <strong>Asisten CSIRT Kalselprov</strong>. 👋<br><br>
                            Saya bantu Anda <strong>melaporkan insiden</strong>, <strong>memahami langkah penanganan</strong>, dan <strong>melacak laporan</strong>.<br><br>
                            Pilih topik di bawah atau ketik pertanyaan Anda.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic suggestions from last bot reply -->
        <div x-show="suggestions.length > 0" class="px-3 pt-2 pb-1 bg-slate-50 dark:bg-slate-950/50 border-t border-slate-100 dark:border-slate-800 shrink-0">
            <div class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1.5">Saran lanjutan</div>
            <div class="flex gap-1.5 overflow-x-auto pb-1 scrollbar-thin">
                <template x-for="chip in suggestions" :key="chip">
                    <button type="button"
                            @click="sendQuickMessage(chip)"
                            :disabled="isLoading"
                            class="chatbot-chip px-2.5 py-1 text-[11px] font-bold rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30 hover:bg-emerald-100 dark:hover:bg-emerald-500/20 transition-colors disabled:opacity-50"
                            x-text="chip"></button>
                </template>
            </div>
        </div>

        <!-- Quick topics (always visible) -->
        <div class="px-3 py-2 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 shrink-0">
            <div class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-1.5">Topik populer</div>
            <div class="flex gap-1.5 overflow-x-auto pb-1">
                @foreach($quickTopics as $topic)
                    <button type="button"
                            @click="sendQuickMessage(@js($topic['message']))"
                            :disabled="isLoading"
                            class="chatbot-chip px-2.5 py-1 text-[11px] font-bold rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-emerald-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors disabled:opacity-50">
                        {{ $topic['label'] }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Typing -->
        <div x-show="isLoading" x-cloak class="px-4 py-2 flex items-center gap-2 bg-slate-100 dark:bg-slate-900 shrink-0">
            <div class="flex gap-1">
                <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.15s"></div>
                <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
            </div>
            <span class="text-[10px] text-slate-500">Menyusun jawaban...</span>
        </div>

        <!-- Input -->
        <div class="border-t border-slate-200 dark:border-slate-800 p-3 bg-white dark:bg-slate-900 shrink-0">
            <form @submit.prevent="sendMessage()" class="flex gap-2">
                <input type="text"
                       x-model="message"
                       x-ref="chatInput"
                       @keydown.enter.prevent="sendMessage()"
                       placeholder="Tanya solusi atau cara melaporkan..."
                       class="flex-1 px-3 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40"
                       :disabled="isLoading">
                <button type="submit"
                        :disabled="isLoading || !message.trim()"
                        class="bg-emerald-600 hover:bg-emerald-500 text-white px-3 py-2 rounded-xl transition-all disabled:opacity-40 shrink-0"
                        aria-label="Kirim pesan">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
window.chatbotData = function chatbotData() {
    return {
        isOpen: false,
        message: '',
        isLoading: false,
        suggestions: @js(config('chatbot.default_suggestions', [])),

        get chatbotUrl() {
            return document.getElementById('chatbot-widget')?.dataset.chatbotUrl || '';
        },

        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.scrollToBottom();
                    this.$refs.chatInput?.focus();
                });
            }
        },

        sendQuickMessage(text) {
            this.message = text;
            this.sendMessage();
        },

        async sendMessage() {
            if (!this.message.trim() || this.isLoading) return;

            const userMessage = this.message.trim();
            this.message = '';
            this.addMessage(userMessage, 'user');
            this.isLoading = true;

            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const response = await fetch(this.chatbotUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: userMessage }),
                });

                const data = await response.json();

                if (data.success) {
                    this.addMessage(data.response, 'bot', data.actions || []);
                    if (Array.isArray(data.suggestions) && data.suggestions.length) {
                        this.suggestions = data.suggestions;
                    }
                } else {
                    this.addMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot');
                }
            } catch (error) {
                console.error('Chatbot error:', error);
                this.addMessage('Koneksi gagal. Periksa internet Anda dan coba lagi.', 'bot');
            } finally {
                this.isLoading = false;
            }
        },

        renderMarkdown(text) {
            let html = text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');

            html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="text-emerald-700 dark:text-emerald-300">$1</strong>');
            html = html.replace(/\*(.*?)\*/g, '<em>$1</em>');

            const lines = html.split('\n');
            let result = '';
            let inList = false;

            lines.forEach((line) => {
                const trimmed = line.trim();
                if (trimmed.startsWith('- ')) {
                    if (!inList) {
                        result += '<ul>';
                        inList = true;
                    }
                    result += '<li>' + trimmed.slice(2) + '</li>';
                } else {
                    if (inList) {
                        result += '</ul>';
                        inList = false;
                    }
                    if (trimmed.startsWith('|')) return;
                    if (trimmed === '') {
                        result += '<br>';
                    } else {
                        result += trimmed + '<br>';
                    }
                }
            });

            if (inList) result += '</ul>';
            return result;
        },

        addMessage(text, type, actions = []) {
            const container = document.getElementById('chatbot-messages');
            const wrap = document.createElement('div');
            wrap.className = type === 'user'
                ? 'flex items-start gap-2 flex-row-reverse'
                : 'flex items-start gap-2';

            const avatar = document.createElement('div');
            avatar.className = type === 'user'
                ? 'w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center shrink-0'
                : 'w-8 h-8 bg-emerald-100 dark:bg-emerald-500/20 rounded-full flex items-center justify-center shrink-0';
            avatar.innerHTML = type === 'user'
                ? '<svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>'
                : '<span class="text-emerald-700 dark:text-emerald-300 text-[10px] font-black">CS</span>';

            const col = document.createElement('div');
            col.className = type === 'user' ? 'flex flex-col items-end max-w-[85%]' : 'flex flex-col max-w-[85%]';

            const bubble = document.createElement('div');
            bubble.className = type === 'user'
                ? 'bg-blue-600 text-white rounded-2xl rounded-tr-sm px-3 py-2 shadow-md'
                : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl rounded-tl-sm px-3 py-2 shadow-sm';

            const content = document.createElement('div');
            content.className = 'chatbot-msg text-sm leading-relaxed ' + (type === 'user' ? 'text-white' : 'text-slate-700 dark:text-slate-200');
            content.innerHTML = type === 'user' ? text.replace(/</g, '&lt;').replace(/>/g, '&gt;') : this.renderMarkdown(text);
            bubble.appendChild(content);
            col.appendChild(bubble);

            if (type === 'bot' && actions.length) {
                const actionRow = document.createElement('div');
                actionRow.className = 'flex flex-wrap gap-1.5 mt-2';
                actions.forEach((action) => {
                    const link = document.createElement('a');
                    link.href = action.url;
                    link.target = '_blank';
                    link.rel = 'noopener';
                    const isPrimary = action.style === 'primary';
                    link.className = isPrimary
                        ? 'inline-flex items-center px-2.5 py-1 text-[10px] font-black uppercase tracking-wide rounded-lg bg-emerald-600 text-white hover:bg-emerald-500 transition-colors'
                        : 'inline-flex items-center px-2.5 py-1 text-[10px] font-black uppercase tracking-wide rounded-lg bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-300 dark:hover:bg-slate-600 transition-colors';
                    link.textContent = action.label;
                    actionRow.appendChild(link);
                });
                col.appendChild(actionRow);
            }

            wrap.appendChild(avatar);
            wrap.appendChild(col);
            container.appendChild(wrap);
            this.scrollToBottom();
        },

        scrollToBottom() {
            const el = document.getElementById('chatbot-messages');
            if (el) el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' });
        },
    };
};

document.addEventListener('DOMContentLoaded', () => {
    const widget = document.getElementById('chatbot-widget');
    if (widget && widget.parentElement !== document.body) {
        document.body.appendChild(widget);
    }
});
</script>
