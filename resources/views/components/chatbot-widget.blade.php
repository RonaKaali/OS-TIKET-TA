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
</style>
<div id="chatbot-widget" 
     data-chatbot-url="{{ route('chatbot.message') }}"
     x-data="chatbotData()"
     style="position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 99999 !important; display: block !important; visibility: visible !important; margin: 0 !important; padding: 0 !important; overflow: visible !important;">
    <!-- Chatbot Button -->
    <button id="chatbot-button"
            @click="toggleChat()" 
            class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-full w-16 h-16 shadow-2xl hover:shadow-3xl transition-all transform hover:scale-110 flex items-center justify-center group"
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
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-4"
         class="absolute bottom-20 right-0 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col overflow-hidden"
         style="display: none; position: absolute !important; bottom: 80px !important; right: 0 !important; height: 500px !important; max-height: calc(100vh - 120px) !important; max-width: calc(100vw - 48px) !important; overflow: visible !important; clip: auto !important; clip-path: none !important; z-index: 1 !important;">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <div>
                    <div class="font-semibold">CSIRT Kalselprov</div>
                    <div class="text-xs text-blue-100">Online</div>
                </div>
            </div>
        </div>

        <!-- Messages Container -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="chatbot-messages">
            <!-- Welcome Message -->
            <div class="flex items-start space-x-2">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">CS</span>
                </div>
                <div class="flex-1">
                    <div class="bg-white rounded-lg p-3 shadow-sm border border-gray-200">
                        <p class="text-sm text-gray-800">
                            Halo! Selamat datang di CSIRT Kalselprov. 😊<br><br>
                            Saya siap membantu Anda dengan:<br>
                            • Membuat tiket baru<br>
                            • Mengecek status tiket<br>
                            • Memberikan informasi<br><br>
                            Ketik /help untuk bantuan lebih lanjut.
                        </p>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">Sekarang</div>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="border-t border-gray-200 p-4 bg-white">
            <form @submit.prevent="sendMessage()" class="flex space-x-2">
                <input 
                    type="text" 
                    x-model="message"
                    @keydown.enter.prevent="sendMessage()"
                    placeholder="Ketik pesan Anda..."
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :disabled="isLoading">
                <button 
                    type="submit"
                    :disabled="isLoading || !message.trim()"
                    class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white px-4 py-2 rounded-lg hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            // Pastikan chat window tidak terpotong oleh viewport
            this.$nextTick(() => {
                const chatWindow = this.$el.querySelector('div[x-show]');
                if (chatWindow && this.isOpen) {
                    const rect = chatWindow.getBoundingClientRect();
                    const viewportWidth = window.innerWidth;
                    const viewportHeight = window.innerHeight;
                    
                    // Reset positioning
                    chatWindow.style.left = 'auto';
                    chatWindow.style.top = 'auto';
                    chatWindow.style.right = '0px';
                    chatWindow.style.bottom = '80px';
                    
                    // Jika chat window keluar dari viewport, sesuaikan posisinya
                    if (rect.right > viewportWidth) {
                        chatWindow.style.right = '0px';
                        chatWindow.style.left = 'auto';
                    }
                    if (rect.left < 0) {
                        chatWindow.style.left = '24px';
                        chatWindow.style.right = 'auto';
                    }
                    if (rect.bottom > viewportHeight) {
                        chatWindow.style.bottom = '80px';
                        chatWindow.style.top = 'auto';
                        chatWindow.style.maxHeight = (viewportHeight - 120) + 'px';
                    }
                    if (rect.top < 0) {
                        chatWindow.style.top = '24px';
                        chatWindow.style.bottom = 'auto';
                        chatWindow.style.maxHeight = (viewportHeight - 48) + 'px';
                    }
                    
                    // Pastikan tidak terpotong
                    chatWindow.style.overflow = 'visible';
                    chatWindow.style.clip = 'auto';
                    chatWindow.style.clipPath = 'none';
                    chatWindow.style.maxWidth = Math.min(384, viewportWidth - 48) + 'px';
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
                    const formattedResponse = data.response.replace(/\n/g, '<br>');
                    this.addMessage(formattedResponse, 'bot');
                } else {
                    this.addMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot');
                }
            } catch (error) {
                console.error('Error:', error);
                this.addMessage('Maaf, terjadi kesalahan. Silakan coba lagi.', 'bot');
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
            
            const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            
            const avatarDiv = document.createElement('div');
            avatarDiv.className = type === 'user' 
                ? 'w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center flex-shrink-0'
                : 'w-8 h-8 bg-gradient-to-r from-blue-600 to-indigo-700 rounded-full flex items-center justify-center flex-shrink-0';
            
            if (type === 'user') {
                avatarDiv.innerHTML = '<svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>';
            } else {
                avatarDiv.innerHTML = '<span class="text-white text-xs font-bold">CS</span>';
            }
            
            const contentDiv = document.createElement('div');
            contentDiv.className = type === 'user' ? 'flex-1 flex flex-col items-end' : 'flex-1';
            
            const messageBubble = document.createElement('div');
            messageBubble.className = type === 'user' 
                ? 'bg-blue-600 text-white rounded-lg p-3 shadow-sm border border-gray-200 max-w-[80%]'
                : 'bg-white rounded-lg p-3 shadow-sm border border-gray-200 max-w-[80%]';
            
            const messageText = document.createElement('p');
            messageText.className = type === 'user' ? 'text-sm text-white' : 'text-sm text-gray-800';
            messageText.innerHTML = text;
            messageBubble.appendChild(messageText);
            
            const timeDiv = document.createElement('div');
            timeDiv.className = 'text-xs text-gray-500 mt-1';
            timeDiv.textContent = time;
            
            contentDiv.appendChild(messageBubble);
            contentDiv.appendChild(timeDiv);
            
            messageDiv.appendChild(avatarDiv);
            messageDiv.appendChild(contentDiv);
            
            messagesContainer.appendChild(messageDiv);
            this.$nextTick(() => this.scrollToBottom());
        },
        scrollToBottom() {
            const messagesContainer = document.getElementById('chatbot-messages');
            if (messagesContainer) {
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }
        }
    };
};

// Debug: Pastikan widget terlihat dan tidak terpotong
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Mencari chatbot widget...');
    const widget = document.getElementById('chatbot-widget');
    if (widget) {
        console.log('✅ Chatbot widget ditemukan di DOM');
        
        // Pastikan widget berada langsung di body (bukan di dalam container)
        if (widget.parentElement !== document.body) {
            console.log('Memindahkan widget ke body...');
            document.body.appendChild(widget);
        }
        
        // Set semua style penting
        widget.style.cssText = `
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
            max-width: none !important;
            max-height: none !important;
        `;
        
        console.log('Widget position:', window.getComputedStyle(widget).position);
        console.log('Widget display:', window.getComputedStyle(widget).display);
        console.log('Widget visibility:', window.getComputedStyle(widget).visibility);
        console.log('Widget z-index:', window.getComputedStyle(widget).zIndex);
        
        // Pastikan parent body/html tidak memotong
        document.body.style.overflowX = 'visible';
        document.documentElement.style.overflowX = 'visible';
        
        // Pastikan tidak ada container yang memotong
        let parent = widget.parentElement;
        while (parent && parent !== document.body) {
            const computedStyle = window.getComputedStyle(parent);
            if (computedStyle.overflow === 'hidden' || computedStyle.overflowX === 'hidden') {
                console.warn('Parent dengan overflow hidden ditemukan:', parent);
                parent.style.overflow = 'visible';
                parent.style.overflowX = 'visible';
            }
            parent = parent.parentElement;
        }
        
        // Tambahkan event listener untuk resize window
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                const widget = document.getElementById('chatbot-widget');
                if (widget) {
                    const chatWindow = widget.querySelector('div[x-show]');
                    if (chatWindow && window.getComputedStyle(chatWindow).display !== 'none') {
                        // Chat window terbuka, sesuaikan posisi
                        const rect = chatWindow.getBoundingClientRect();
                        const viewportWidth = window.innerWidth;
                        const viewportHeight = window.innerHeight;
                        
                        if (rect.right > viewportWidth) {
                            chatWindow.style.right = '0px';
                            chatWindow.style.left = 'auto';
                        }
                        if (rect.left < 0) {
                            chatWindow.style.left = '24px';
                            chatWindow.style.right = 'auto';
                        }
                        if (rect.bottom > viewportHeight) {
                            chatWindow.style.maxHeight = (viewportHeight - 120) + 'px';
                        }
                        chatWindow.style.maxWidth = Math.min(384, viewportWidth - 48) + 'px';
                    }
                }
            }, 100);
        });
    } else {
        console.error('❌ Chatbot widget TIDAK ditemukan di DOM!');
        console.log('Mencari semua elemen dengan id chatbot...');
        const allElements = document.querySelectorAll('[id*="chatbot"]');
        console.log('Elemen yang ditemukan:', allElements);
    }
    
    // Pastikan button terlihat
    const button = document.getElementById('chatbot-button');
    if (button) {
        console.log('✅ Chatbot button ditemukan');
        button.style.display = 'flex';
        button.style.visibility = 'visible';
        button.style.opacity = '1';
    } else {
        console.error('❌ Chatbot button TIDAK ditemukan!');
    }
});
</script>

