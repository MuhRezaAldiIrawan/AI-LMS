@extends('layouts.main')

@section('content')
    <!-- Breadcrumb Start -->
    <div class="breadcrumb mb-24">
        <ul class="flex-align gap-4">
            <li><a href="{{ route('dashboard.index') }}" class="text-gray-200 fw-normal text-15 hover-text-main-600">Home</a></li>
            <li> <span class="text-gray-500 fw-normal d-flex"><i class="ph ph-caret-right"></i></span> </li>
            <li><span class="text-main-600 fw-normal text-15">AI Assistant</span></li>
        </ul>
    </div>
    <!-- Breadcrumb End -->

    <!-- AI Assistant Chat Box Start -->
    <div class="card chat-box w-100">
        <div class="card-header py-16 border-bottom border-gray-100">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-16">
                    <div class="position-relative flex-shrink-0">
                        <div class="w-40 h-40 rounded-circle bg-main-600 flex-center">
                            <i class="ph-fill ph-robot text-white text-20"></i>
                        </div>
                        <span class="activation-badge w-12 h-12 border-2 bg-success position-absolute inset-block-end-0 inset-inline-end-0"></span>
                    </div>
                    <div class="d-flex flex-column">
                        <h6 class="text-line-1 text-15 text-gray-400 fw-bold mb-0">AI Learning Assistant</h6>
                        <span class="text-line-1 text-13 text-gray-200" id="ai-status">Ready to help with your learning</span>
                    </div>
                </div>

                <div class="flex-align gap-16">
                    <div class="dropdown flex-shrink-0">
                        <button class="text-gray-400 text-xl d-flex rounded-4" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ph-fill ph-dots-three-outline-vertical"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu--md border-0 bg-transparent p-0">
                            <div class="card border border-gray-100 rounded-12 box-shadow-custom">
                                <div class="card-body p-12">
                                    <div class="max-h-200 overflow-y-auto scroll-sm pe-8">
                                        <ul>
                                            <li class="mb-0">
                                                <button type="button" id="new-session"
                                                    class="py-6 text-15 px-8 hover-bg-gray-50 text-gray-300 w-100 rounded-8 fw-normal text-xs d-block text-start">
                                                    <span class="text"><i class="ph ph-plus-circle"></i> New Session</span>
                                                </button>
                                            </li>
                                            <li class="mb-0">
                                                <button type="button" id="clear-chat"
                                                    class="delete-item-btn py-6 text-15 px-8 hover-bg-gray-50 text-gray-300 w-100 rounded-8 fw-normal text-xs d-block text-start">
                                                    <span class="text"><i class="ph ph-x-circle"></i> Clear History</span>
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="chat-box-item-wrapper overflow-y-auto scroll-sm" id="chat-messages">
                @if($chatHistory && $chatHistory->count() > 0)
                    @foreach($chatHistory as $chat)
                        @if($chat->message_type === 'user')
                            <div class="chat-box-item right">
                                <div class="w-40 h-40 rounded-circle bg-primary-600 flex-center flex-shrink-0">
                                    <i class="ph ph-user text-white text-20"></i>
                                </div>
                                <div class="chat-box-item__content">
                                    <div class="chat-box-item__text">{{ $chat->message }}</div>
                                    <span>{{ $chat->getFormattedTimeAttribute() }}</span>
                                </div>
                            </div>
                        @else
                            <div class="chat-box-item">
                                <div class="w-40 h-40 rounded-circle bg-main-600 flex-center flex-shrink-0">
                                    <i class="ph-fill ph-robot text-white text-20"></i>
                                </div>
                                <div class="chat-box-item__content">
                                    <div class="chat-box-item__text js-md" data-raw="{{ e($chat->message) }}"></div>
                                    <span>{{ $chat->getFormattedTimeAttribute() }}</span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <!-- Welcome Message from AI (only show if no history) -->
                    <div class="chat-box-item">
                        <div class="w-40 h-40 rounded-circle bg-main-600 flex-center flex-shrink-0">
                            <i class="ph-fill ph-robot text-white text-20"></i>
                        </div>
                        <div class="chat-box-item__content">
                            <div class="chat-box-item__text">
                                <strong>Hello {{ Auth::user()->name }}! I'm your AI Learning Assistant 🤖</strong><br>
                                I'm here to help you with your courses, answer questions about lessons, assist with quizzes, and guide you through your learning journey. How can I help you today?
                            </div>
                            <span>Just now</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <div class="card-footer border-top border-gray-100">
            <form id="chat-form" class="flex-align gap-8 chat-box-bottom">
                @csrf
                <label for="fileUp"
                    class="flex-shrink-0 file-btn w-48 h-48 flex-center bg-main-50 text-24 text-main-600 rounded-circle hover-bg-main-100 transition-2"
                    title="Attach file or image">
                    <i class="ph ph-paperclip"></i>
                </label>
                <input type="file" name="fileName" id="fileUp" hidden>
                <div class="position-relative flex-grow-1">
                    <input type="text" id="question-input" name="question"
                        class="form-control h-48 border-transparent px-20 focus-border-main-600 bg-main-50 rounded-pill placeholder-15 pe-60"
                        placeholder="Ask me anything about your courses, lessons, or get help with assignments..."
                        required maxlength="2000">
                    <div class="position-absolute top-50 end-0 translate-middle-y me-16">
                        <div class="dropdown">
                            <button class="btn btn-sm bg-transparent border-0 p-0" type="button" data-bs-toggle="dropdown" title="Quick suggestions">
                                <i class="ph ph-lightbulb text-main-600 text-18"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item text-13 quick-suggestion" href="#" data-suggestion="Explain this concept">Explain this concept</a></li>
                                <li><a class="dropdown-item text-13 quick-suggestion" href="#" data-suggestion="Quiz me on this topic">Quiz me on this topic</a></li>
                                <li><a class="dropdown-item text-13 quick-suggestion" href="#" data-suggestion="Show my progress">Show my progress</a></li>
                                <li><a class="dropdown-item text-13 quick-suggestion" href="#" data-suggestion="Recommend next course">Recommend next course</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <button type="submit" id="send-btn"
                    class="flex-shrink-0 submit-btn btn btn-main rounded-pill flex-align gap-4 py-15">
                    <span class="btn-text">Ask AI</span>
                    <span class="d-flex text-md d-sm-flex d-none"><i class="ph-fill ph-paper-plane-tilt"></i></span>
                </button>
            </form>
        </div>
    </div>
    <!-- AI Assistant Chat Box End -->

    <style>
    /* Chat Container */
    .chat-box-item-wrapper {
        height: calc(100vh - 200px);
        background: #f8fafc;
        border-radius: 12px 12px 0 0;
        position: relative;
        padding: 20px 0;
    }

    /* General Chat Item */
    .chat-box-item {
        margin-bottom: 20px;
        width: 100%;
        display: flex;
        align-items: flex-end;
        gap: 12px;
        padding: 0 20px;
        animation: fadeInUp 0.3s ease-out;
    }

    /* AI Messages (Left side) */
    .chat-box-item:not(.right) {
        justify-content: flex-start;
    }

    .chat-box-item:not(.right) .chat-box-item__content {
        margin-right: auto;
        max-width: 75%;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
    }

    .chat-box-item:not(.right) .chat-box-item__content span {
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
        display: block;
    }

    /* User Messages (Right side) */
    .chat-box-item.right {
        flex-direction: row-reverse;
        justify-content: flex-start;
    }

    .chat-box-item.right .chat-box-item__content {
        margin-left: auto;
        margin-right: 0;
        max-width: 75%;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .chat-box-item.right .chat-box-item__content span {
        text-align: right;
        font-size: 12px;
        color: #64748b;
        margin-top: 4px;
    }

    /* Avatar Styling */
    .chat-box-item .w-40 {
        width: 40px !important;
        height: 40px !important;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* Chat Bubble Base Styling */
    .chat-box-item__text {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 12px 16px;
        border-radius: 18px;
        word-wrap: break-word;
        line-height: 1.5;
        font-size: 14px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        margin-bottom: 4px;
        min-height: auto;
        display: block;
        position: relative;
    }

    /* AI Chat Bubble */
    .chat-box-item:not(.right) .chat-box-item__text {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-left: 3px solid #3b82f6;
        border-top-left-radius: 4px;
    }

    .chat-box-item:not(.right) .chat-box-item__text::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 12px;
        width: 0;
        height: 0;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-right: 8px solid #ffffff;
    }

    /* User Chat Bubble */
    .chat-box-item.right .chat-box-item__text {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        color: white !important;
        border-color: #2563eb !important;
        border-top-right-radius: 4px;
        border-right: 3px solid #3b82f6;
        text-align: left;
    }

    .chat-box-item.right .chat-box-item__text::after {
        content: '';
        position: absolute;
        right: -8px;
        top: 12px;
        width: 0;
        height: 0;
        border-top: 8px solid transparent;
        border-bottom: 8px solid transparent;
        border-left: 8px solid #2563eb;
    }

    /* Timestamp Styling */
    .chat-box-item__content span {
        font-size: 12px;
        color: #64748b;
        margin-top: 2px;
        opacity: 0.7;
    }

    /* Content Container */
    .chat-box-item__content {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    /* Utility Classes */
    .flex-center {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Loading Animation */
    .typing-indicator {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 8px 0;
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background-color: #94a3b8;
        animation: typing 1.4s infinite;
    }

    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }

    @keyframes typing {
        0%, 60%, 100% {
            transform: translateY(0);
            opacity: 0.4;
        }
        30% {
            transform: translateY(-8px);
            opacity: 1;
        }
    }

    /* Loading Button */
    .loading-btn {
        opacity: 0.7;
        pointer-events: none;
    }

    /* Message Formatting */
    .chat-box-item__text strong {
        font-weight: 600;
    }

    .chat-box-item__text em {
        font-style: italic;
    }

    .chat-box-item__text code {
        background-color: rgba(0, 0, 0, 0.1);
        padding: 2px 6px;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
    }

    .chat-box-item.right .chat-box-item__text code {
        background-color: rgba(255, 255, 255, 0.2);
    }

    /* List styling inside chat bubbles */
    .chat-box-item__text ul,
    .chat-box-item__text ol {
        margin: 8px 0 8px 18px;
        padding-left: 18px;
    }
    .chat-box-item__text li {
        margin: 4px 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .chat-box-item__content {
            max-width: 85%;
        }

        .chat-box-item__text {
            font-size: 13px;
            padding: 10px 14px;
        }

        .chat-box-item-wrapper {
            padding: 16px;
        }
    }

    /* Scrollbar Styling */
    .chat-box-item-wrapper::-webkit-scrollbar {
        width: 6px;
    }

    .chat-box-item-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .chat-box-item-wrapper::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .chat-box-item-wrapper::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Animation untuk pesan baru */
    .chat-box-item {
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatForm = document.getElementById('chat-form');
        const questionInput = document.getElementById('question-input');
        const chatMessages = document.getElementById('chat-messages');
        const sendBtn = document.getElementById('send-btn');
        const clearChatBtn = document.getElementById('clear-chat');
        const newSessionBtn = document.getElementById('new-session');
        const aiStatus = document.getElementById('ai-status');
        const quickSuggestions = document.querySelectorAll('.quick-suggestion');

        // Get session ID from server or create new
        let currentSessionId = '{{ $sessionId ?? '' }}';

        // Handle quick suggestions
        quickSuggestions.forEach(suggestion => {
            suggestion.addEventListener('click', function(e) {
                e.preventDefault();
                questionInput.value = this.dataset.suggestion;
                questionInput.focus();
            });
        });

        // Handle new session
        newSessionBtn.addEventListener('click', async function() {
            try {
                const response = await fetch('{{ route("aiassistant.new-session") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    }
                });

                const data = await response.json();
                if (response.ok && data.session_id) {
                    currentSessionId = data.session_id;
                    // Clear current chat display
                    showWelcomeMessage();
                    // Update URL with new session
                    const url = new URL(window.location);
                    url.searchParams.set('session_id', currentSessionId);
                    window.history.pushState({}, '', url);
                }
            } catch (error) {
                console.error('Error creating new session:', error);
            }
        });

        // Handle clear chat history
        clearChatBtn.addEventListener('click', async function() {
            if (confirm('Are you sure you want to clear the chat history? This action cannot be undone.')) {
                try {
                    const response = await fetch('{{ route("aiassistant.clear") }}', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: JSON.stringify({ session_id: currentSessionId })
                    });

                    if (response.ok) {
                        showWelcomeMessage();
                    }
                } catch (error) {
                    console.error('Error clearing history:', error);
                }
            }
        });

        // Handle form submission
        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const question = questionInput.value.trim();
            if (!question) return;

            // Add user message to chat
            addUserMessage(question);

            // Clear input and show loading
            questionInput.value = '';
            setLoading(true);

            // Show typing indicator
            const typingId = showTypingIndicator();

            try {
                const response = await fetch('{{ route("aiassistant.ask") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        question: question,
                        session_id: currentSessionId
                    })
                });

                const data = await response.json();

                // Remove typing indicator
                removeTypingIndicator(typingId);

                if (response.ok && data.answer) {
                    addAiMessage(data.answer, data.timestamp);
                    // Update session ID if returned
                    if (data.session_id) {
                        currentSessionId = data.session_id;
                    }
                } else {
                    addAiMessage(data.error || 'Maaf, terjadi kesalahan. Silakan coba lagi.');
                }
            } catch (error) {
                // console.error('Error:', error);
                removeTypingIndicator(typingId);
                addAiMessage('Maaf, terjadi kesalahan koneksi. Silakan coba lagi.');
            }

            setLoading(false);
        });

        // Handle Enter key
        questionInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });

        function showWelcomeMessage() {
            chatMessages.innerHTML = `
                <div class="chat-box-item">
                    <div class="w-40 h-40 rounded-circle bg-main-600 flex-center flex-shrink-0">
                        <i class="ph-fill ph-robot text-white text-20"></i>
                    </div>
                    <div class="chat-box-item__content">
                        <div class="chat-box-item__text">
                            <strong>Hello {{ Auth::user()->name }}! I'm your AI Learning Assistant 🤖</strong><br>
                            I'm here to help you with your courses, answer questions about lessons, assist with quizzes, and guide you through your learning journey. How can I help you today?
                        </div>
                        <span>Just now</span>
                    </div>
                </div>
            `;
        }

        function addUserMessage(message) {
            const timestamp = new Date().toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const userMessage = `
                <div class="chat-box-item right">
                    <div class="w-40 h-40 rounded-circle bg-primary-600 flex-center flex-shrink-0">
                        <i class="ph ph-user text-white text-20"></i>
                    </div>
                    <div class="chat-box-item__content">
                        <div class="chat-box-item__text">${escapeHtml(message)}</div>
                        <span>${timestamp}</span>
                    </div>
                </div>
            `;

            chatMessages.insertAdjacentHTML('beforeend', userMessage);
            scrollToBottom();
        }

        function addAiMessage(message, timestamp = null) {
            const time = timestamp || new Date().toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit'
            });

            const aiMessage = `
                <div class="chat-box-item">
                    <div class="w-40 h-40 rounded-circle bg-main-600 flex-center flex-shrink-0">
                        <i class="ph-fill ph-robot text-white text-20"></i>
                    </div>
                    <div class="chat-box-item__content">
                        <div class="chat-box-item__text">${formatMessage(message)}</div>
                        <span>${time}</span>
                    </div>
                </div>
            `;

            chatMessages.insertAdjacentHTML('beforeend', aiMessage);
            scrollToBottom();
        }

        function showTypingIndicator() {
            const typingId = 'typing-' + Date.now();
            const typingIndicator = `
                <div class="chat-box-item" id="${typingId}">
                    <div class="w-40 h-40 rounded-circle bg-main-600 flex-center flex-shrink-0">
                        <i class="ph-fill ph-robot text-white text-20"></i>
                    </div>
                    <div class="chat-box-item__content">
                        <div class="chat-box-item__text">
                            <div class="typing-indicator">
                                <div class="typing-dot"></div>
                                <div class="typing-dot"></div>
                                <div class="typing-dot"></div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            chatMessages.insertAdjacentHTML('beforeend', typingIndicator);
            scrollToBottom();
            return typingId;
        }

        function removeTypingIndicator(typingId) {
            const typingElement = document.getElementById(typingId);
            if (typingElement) {
                typingElement.remove();
            }
        }

        function setLoading(loading) {
            if (loading) {
                sendBtn.classList.add('loading-btn');
                sendBtn.querySelector('.btn-text').textContent = 'Sending...';
                aiStatus.textContent = 'Typing...';
                questionInput.disabled = true;
            } else {
                sendBtn.classList.remove('loading-btn');
                sendBtn.querySelector('.btn-text').textContent = 'Ask AI';
                aiStatus.textContent = 'Ready to help with your learning';
                questionInput.disabled = false;
                questionInput.focus();
            }
        }

        function scrollToBottom() {
            setTimeout(() => {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }, 100);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Render a safe subset of Markdown (bold, inline code, basic lists, line breaks)
        function formatMessage(message) {
            // 1) Escape any HTML to prevent injection
            const div = document.createElement('div');
            div.textContent = message ?? '';
            const safe = div.innerHTML;

            // 2) Build lists and inline formatting line-by-line
            const lines = safe.split(/\r?\n/);
            let html = '';
            let inUl = false;
            let inOl = false;

            const flushLists = () => {
                if (inUl) { html += '</ul>'; inUl = false; }
                if (inOl) { html += '</ol>'; inOl = false; }
            };

            for (let rawLine of lines) {
                const liBullet = /^\s*[\-*]\s+(.+)/.exec(rawLine);
                const liNumber = /^\s*(\d+)\.\s+(.+)/.exec(rawLine);

                if (liBullet) {
                    if (inOl) { html += '</ol>'; inOl = false; }
                    if (!inUl) { html += '<ul>'; inUl = true; }
                    const content = liBullet[1]
                        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                        .replace(/`(.+?)`/g, '<code>$1</code>');
                    html += `<li>${content}</li>`;
                    continue;
                }

                if (liNumber) {
                    if (inUl) { html += '</ul>'; inUl = false; }
                    if (!inOl) { html += '<ol>'; inOl = true; }
                    const content = liNumber[2]
                        .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                        .replace(/`(.+?)`/g, '<code>$1</code>');
                    html += `<li>${content}</li>`;
                    continue;
                }

                // Normal line (no list)
                flushLists();
                const line = rawLine
                    .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                    // keep single-asterisk emphasis minimal, but avoid matching bullets
                    .replace(/(^|[^*])\*(?!\s)([^*]+?)\*(?!\*)/g, '$1<em>$2</em>')
                    .replace(/`(.+?)`/g, '<code>$1</code>');
                html += line + '<br>';
            }

            flushLists();
            return html;
        }

        function getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        }

        // Auto scroll to bottom on page load
        scrollToBottom();

        // Focus on input when page loads
        questionInput.focus();

        // Render markdown for any preloaded AI history messages
        document.querySelectorAll('.chat-box-item__text.js-md[data-raw]')
            .forEach(el => { el.innerHTML = formatMessage(el.getAttribute('data-raw')); });
    });
    </script>
@endsection
