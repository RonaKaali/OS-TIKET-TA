<style>
    [x-cloak] { display: none !important; }
    
    /* Pastikan widget tidak terpotong oleh parent container */
    html, body {
        overflow-x: visible !important;
    }
    
    #chatbot-widget { 
        position: fixed !important;
        bottom: 24px !important;
        right: 24px !important;
        z-index: 99999 !important;
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
        clip: auto !important;
        clip-path: none !important;
    }
    
    #chatbot-button {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        width: 64px !important;
        height: 64px !important;
        min-width: 64px !important;
        min-height: 64px !important;
        flex-shrink: 0 !important;
    }
    
    /* Pastikan chat window tidak terpotong */
    #chatbot-widget > div[x-show] {
        overflow: visible !important;
        clip: auto !important;
        clip-path: none !important;
        position: absolute !important;
        bottom: 80px !important;
        right: 0 !important;
        max-height: calc(100vh - 120px) !important;
        max-width: calc(100vw - 48px) !important;
        z-index: 1 !important;
    }
    
    /* Pastikan chat window responsive dan tidak keluar viewport */
    @media (max-width: 640px) {
        #chatbot-widget {
            bottom: 16px !important;
            right: 16px !important;
        }
        #chatbot-widget > div[x-show] {
            width: calc(100vw - 32px) !important;
            max-width: calc(100vw - 32px) !important;
            right: 0 !important;
            bottom: 72px !important;
        }
    }
    
    /* Pastikan konten dalam chat window bisa di-scroll */
    #chatbot-messages {
        overflow-y: auto !important;
        overflow-x: hidden !important;
    }

    @keyframes pulse-custom {
        0% { transform: scale(1); box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }
        50% { transform: scale(1.05); box-shadow: 0 0 30px rgba(16, 185, 129, 0.6); }
        100% { transform: scale(1); box-shadow: 0 0 20px rgba(16, 185, 129, 0.3); }
    }

    .animate-pulse-custom {
        animation: pulse-custom 2s infinite ease-in-out;
    }
</style>
<div id="chatbot-widget" 
     data-chatbot-url="{{ route('chatbot.message') }}"
     x-data="chatbotData()"
     style="position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 99999 !important; display: block !important; visibility: visible !important; margin: 0 !important; padding: 0 !important; overflow: visible !important;">
    <!-- Chatbot Button -->
    <button id="chatbot-button"
            @click="toggleChat()" 
            :class="{'animate-pulse-custom': !isOpen}"
            class="bg-white dark:bg-slate-800 border-2 border-emerald-500/50 text-emerald-600 dark:text-emerald-400 rounded-full w-16 h-16 shadow-lg dark:shadow-[0_0_20px_rgba(16,185,129,0.3)] hover:shadow-xl dark:hover:shadow-[0_0_30px_rgba(16,185,129,0.5)] transition-all transform hover:scale-110 flex items-center justify-center group"
            style="display: flex !important; visibility: visible !important;">
        <svg x-show="!isOpen" 
             class="w-8 h-8" 
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24"
             style="display: block;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <svg x-show="isOpen" 
             x-cloak
             class="w-8 h-8" 
             fill="none" 
             stroke="currentColor" 
             viewBox="0 0 24 24"
             style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>

    <!-- Chat Window -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="absolute bottom-20 right-0 w-80 sm:w-96 bg-white dark:bg-slate-900/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700/50 flex flex-col overflow-hidden transition-all"
         style="display: none; position: absolute !important; bottom: 80px !important; right: 0 !important; height: 500px !important; max-height: calc(100vh - 120px) !important; max-width: calc(100vw - 48px) !important; overflow: visible !important; clip: auto !important; clip-path: none !important; z-index: 1 !important;">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-900 border-b border-slate-200 dark:border-slate-700/50 p-4 flex items-center justify-between transition-colors">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-full flex items-center justify-center relative shadow-inner">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-slate-900 rounded-full"></span>
                </div>
                <div>
                    <div class="font-bold text-slate-900 dark:text-slate-100 tracking-wide text-sm transition-colors">Assistant Kamu</div>
                    <div class="flex items-center space-x-1">
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-tighter transition-colors">Secure Connection</span>
                    </div>
                </div>
            </div>
            <button @click="toggleChat()" class="text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <!-- Messages Container -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-slate-50 dark:bg-slate-900/50 transition-colors" id="chatbot-messages">
            <!-- Welcome Message -->
            <div class="flex items-start space-x-2">
                <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-full flex items-center justify-center flex-shrink-0 shadow-sm transition-all">
                    <span class="text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">AI</span>
                </div>
                <div class="flex-1">
                    <div class="bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/50 rounded-2xl rounded-tl-none p-3 shadow-sm transition-all">
                        <p class="text-sm text-slate-700 dark:text-slate-200 leading-relaxed transition-colors">
                            Halo! Saya **Assistant Kamu**. 🛡️<br><br>
                            Saya menggunakan framework **Zero Trust** dan **CIA Triad** untuk membantu menjaga keamanan Anda. <br><br>
                            Ketik **'help'** untuk bantuan cepat atau tanyakan apapun terkait keamanan siber.
                        </p>
                    </div>
                    <div class="text-[10px] text-emerald-600 dark:text-emerald-500/50 mt-1 ml-1 font-mono uppercase tracking-tighter transition-colors">System Authenticated</div>
                </div>
            </div>
        </div>

        <!-- Typing Indicator (Hidden by default) -->
        <div id="chatbot-typing" x-show="isLoading" class="px-4 py-2 flex items-center space-x-2 bg-slate-100 dark:bg-slate-900/30 transition-colors" style="display: none;">
            <div class="flex space-x-1">
                <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
            </div>
            <span class="text-[10px] text-slate-500 font-mono italic transition-colors">Agent is analyzing...</span>
        </div>

        <!-- Input Area -->
        <div class="border-t border-slate-200 dark:border-slate-800 p-4 bg-white dark:bg-slate-900/80 transition-colors">
            <form @submit.prevent="sendMessage()" class="flex space-x-2">
                <input 
                    type="text" 
                    x-model="message"
                    @keydown.enter.prevent="sendMessage()"
                    placeholder="Analisis keamanan..."
                    class="flex-1 px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/50 rounded-xl text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-600 focus:outline-none focus:ring-1 focus:ring-emerald-500/50 focus:border-emerald-500/30 text-sm transition-all shadow-inner"
                    :disabled="isLoading">
                <button 
                    type="submit"
                    :disabled="isLoading || !message.trim()"
                    class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl shadow-lg shadow-emerald-500/20 dark:shadow-emerald-900/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed group flex items-center justify-center">
                    <svg x-show="!isLoading" class="w-5 h-5 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <svg x-show="isLoading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Pastikan function terdefinisi sebelum Alpine.js mencoba menggunakannya
window.chatbotData = function chatbotData() {
    return {
        isOpen: false,
        message: '',
        isLoading: false,
        get chatbotUrl() {
            const widget = document.getElementById('chatbot-widget');
            return widget ? widget.dataset.chatbotUrl : '';
        },
        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.scrollToBottom();
                    this.adjustChatWindowPosition();
                });
            }
        },
        adjustChatWindowPosition() {
            this.$nextTick(() => {
                const chatWindow = this.$el.querySelector('div[x-show]');
                if (chatWindow && this.isOpen) {
                    const viewportWidth = window.innerWidth;
                    const viewportHeight = window.innerHeight;
                    
                    chatWindow.style.right = '0px';
                    chatWindow.style.bottom = '80px';
                    chatWindow.style.maxWidth = Math.min(400, viewportWidth - 48) + 'px';
                }
            });
        },
        async sendMessage() {
            if (!this.message.trim() || this.isLoading) return;
            const userMessage = this.message.trim();
            this.message = '';
            this.addMessage(userMessage, 'user');
            this.isLoading = true;
            
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch(this.chatbotUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ message: userMessage })
                });
                const data = await response.json();
                if (data.success) {
                    // Support basic markdown rendering for better premium feel
                    let text = data.response;
                    text = text.replace(/\*\*(.*?)\*\*/g, '<b class="text-emerald-400">$1</b>');
                    text = text.replace(/\n/g, '<br>');
                    this.addMessage(text, 'bot');
                } else {
                    this.addMessage('Maaf, terjadi kesalahan komunikasi dengan agent.', 'bot');
                }
            } catch (error) {
                console.error('Error:', error);
                this.addMessage('Maaf, terjadi kesalahan pada neural link.', 'bot');
            } finally {
                this.isLoading = false;
            }
        },
        addMessage(text, type) {
            const messagesContainer = document.getElementById('chatbot-messages');
            const messageDiv = document.createElement('div');
            messageDiv.className = type === 'user' 
                ? 'flex items-start space-x-2 flex-row-reverse space-x-reverse' 
                : 'flex items-start space-x-2';
            
            const avatarDiv = document.createElement('div');
            avatarDiv.className = type === 'user' 
                ? 'w-8 h-8 bg-blue-100 dark:bg-blue-600/20 border border-blue-200 dark:border-blue-500/30 rounded-full flex items-center justify-center flex-shrink-0 shadow-sm transition-all'
                : 'w-8 h-8 bg-emerald-100 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/30 rounded-full flex items-center justify-center flex-shrink-0 shadow-sm transition-all';
            
            if (type === 'user') {
                avatarDiv.innerHTML = '<svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>';
            } else {
                avatarDiv.innerHTML = '<span class="text-emerald-600 dark:text-emerald-400 text-[10px] font-bold">AI</span>';
            }
            
            const contentDiv = document.createElement('div');
            contentDiv.className = type === 'user' ? 'flex-1 flex flex-col items-end' : 'flex-1';
            
            const messageBubble = document.createElement('div');
            messageBubble.className = type === 'user' 
                ? 'bg-blue-600 text-white dark:bg-blue-600/20 dark:text-blue-50 border border-blue-500/30 rounded-2xl rounded-tr-none p-3 shadow-md max-w-[90%] transition-all'
                : 'bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/50 text-slate-800 dark:text-slate-200 rounded-2xl rounded-tl-none p-3 shadow-md max-w-[90%] transition-all';
            
            const messageText = document.createElement('div');
            messageText.className = 'text-sm leading-relaxed';
            messageText.innerHTML = text;
            messageBubble.appendChild(messageText);
            
            contentDiv.appendChild(messageBubble);
            
            messageDiv.appendChild(avatarDiv);
            messageDiv.appendChild(contentDiv);
            
            messagesContainer.appendChild(messageDiv);
            this.$nextTick(() => this.scrollToBottom());
        },
        scrollToBottom() {
            const messagesContainer = document.getElementById('chatbot-messages');
            if (messagesContainer) {
                messagesContainer.scrollTo({
                    top: messagesContainer.scrollHeight,
                    behavior: 'smooth'
                });
            }
        }
    };
};

document.addEventListener('DOMContentLoaded', function() {
    const widget = document.getElementById('chatbot-widget');
    if (widget && widget.parentElement !== document.body) {
        document.body.appendChild(widget);
    }
});
</script>


