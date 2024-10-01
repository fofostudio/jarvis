<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" />


    <title>{{ __('admin.app_tittle') }}</title>

    <!-- Preconnect para mejora de rendimiento -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Fuentes -->
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet">

    <!-- CSS de bibliotecas externas -->

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])




    <!-- CSS personalizado -->
    @include('includes.css_admin')

    <style>
        :root {
            --color-default: #000000;
        }

        body {
            font-family: 'Figtree', sans-serif;
        }

        .mobile-warning-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 0, 0, 0.9);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: white;
            text-align: center;
            font-family: Arial, sans-serif;
        }

        .mobile-warning-overlay h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .mobile-warning-overlay p {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .logo-jarvis {
            width: 200px;
            /* Tamaño predeterminado para escritorio */
        }

        @media (max-width: 768px) {
            .logo-jarvis {
                width: 100px;
                /* Tamaño para dispositivos móviles */
            }
        }
    </style>
    <style>
        .jv-chat-system {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            font-family: Arial, sans-serif;
        }

        .jv-chat-new-chat-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #3c6faa;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .jv-chat-user-list {
            position: absolute;
            bottom: 70px;
            right: 0;
            width: 250px;
            max-height: 300px;
            overflow-y: auto;
            background-color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: none;
        }

        .jv-chat-user-item {
            display: flex;
            align-items: center;
            padding: 10px;
            cursor: pointer;
        }

        .jv-chat-user-item:hover {
            background-color: #f0f0f0;
        }

        .jv-chat-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            background-size: cover;
            background-position: center;
        }

        .jv-chat-bubbles {
            position: fixed;
            bottom: 90px;
            right: 20px;
            display: flex;
            flex-direction: column-reverse;
            align-items: flex-end;
        }

        .jv-chat-bubble {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            margin-bottom: 10px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .jv-chat-window {
            position: fixed;
            bottom: 90px;
            right: 80px;
            width: 300px;
            height: 400px;
            background-color: white;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            display: none;
        }

        .jv-chat-header {
            padding: 10px;
            background-color: #4a90e2;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .jv-chat-header-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .jv-chat-body {
            flex-grow: 1;
            overflow-y: auto;
            padding: 10px;
            display: flex;
            flex-direction: column;
        }

        .jv-chat-message {
            max-width: 70%;
            padding: 8px 12px;
            margin-bottom: 10px;
            border-radius: 18px;
            word-wrap: break-word;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .jv-chat-message.sent {
            align-self: flex-end;
            background-color: #4a90e2;
            color: white;
            border-bottom-right-radius: 4px;
        }

        .jv-chat-message.received {
            align-self: flex-start;
            background-color: #e0e0e0;
            color: black;
            border-bottom-left-radius: 4px;
        }

        .jv-chat-footer {
            padding: 10px;
            border-top: 1px solid #ccc;
            display: flex;
            align-items: center;
        }

        .jv-chat-input {
            flex-grow: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 20px;
            margin-right: 10px;
            resize: none;
            overflow-y: auto;
            max-height: 100px;
        }

        .jv-chat-send-btn {
            width: 36px;
            height: 36px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .jv-chat-send-btn:hover {
            background-color: #3a7bc8;
        }

        .jv-chat-send-btn i {
            font-size: 16px;
        }

        .jv-chat-minimize-btn,
        .jv-chat-close-btn {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 0 5px;
        }
    </style>
    @yield('css')
    @stack('styles')

    @yield('javascriptheader')
    <style>
        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
        }
    </style>
</head>

<body>
    @if (auth()->check() && auth()->user()->role == 'operator' && \Jenssegers\Agent\Facades\Agent::isMobile())
        <div class="mobile-warning-overlay">
            <h1>Acceso no permitido</h1>
            <p>Jarvis no es accesible vía móvil para operadores.</p>
            <p>Por favor, accede desde un dispositivo de escritorio.</p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-dark">
                    {{ __('admin.LogOut') }}
                </button>
            </form>
        </div>
    @else
        <div class="overlay" data-bs-toggle="offcanvas" data-bs-target="#sidebar-nav"></div>
        <div class="popout font-default"></div>

        <main>
            <div class="offcanvas offcanvas-start sidebar bg-dark text-white" tabindex="-1" id="sidebar-nav"
                data-bs-keyboard="false" data-bs-backdrop="false">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title"><img src="{{ asset('images/logojarvis.svg') }}" class="logo-jarvis"
                            alt="Logo Jarvis">
                    </h5>
                    <button type="button" class="btn-close btn-close-custom text-white toggle-menu d-lg-none"
                        data-bs-dismiss="offcanvas" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="offcanvas-body px-0 scrollbar">
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-start list-sidebar" id="menu">
                        <!-- Aquí va el contenido del sidebar -->
                        @include('layouts.sidebar')
                    </ul>
                </div>
            </div>

            <header class="py-3 mb-3 shadow-custom bg-dark">
                <div class="container-fluid px-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <a class="toggle-menu d-block d-lg-none fs-3" data-bs-toggle="offcanvas"
                            data-bs-target="#sidebar-nav" href="#">
                            <i class="bi-list"></i>
                        </a>

                        <a class="animate-up-2" href="{{ url('/') }}">
                        </a>

                        <div class="d-flex align-items-center">
                            <!-- Icono de Notificaciones -->
                            <div class="dropdown me-3">
                                <a href="#" class="text-decoration-none position-relative"
                                    id="dropdownNotifications" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi-bell fs-4"></i>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        3 <!-- Número de notificaciones -->
                                        <span class="visually-hidden">unread notifications</span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow"
                                    aria-labelledby="dropdownNotifications">
                                    <li class="dropdown-item d-flex align-items-center">
                                        <!-- Foto de perfil con la inicial -->
                                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center"
                                            style="width: 40px; height: 40px;">
                                            <span class="fs-5">N</span> <!-- Letra inicial de la notificación -->
                                        </div>
                                        <div class="ms-2">
                                            <strong>Notificación 1</strong>
                                            <div class="text-muted text-sm">Descripción de la notificación 1</div>
                                        </div>
                                    </li>
                                    <li class="dropdown-item d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center"
                                            style="width: 40px; height: 40px;">
                                            <span class="fs-5">A</span>
                                        </div>
                                        <div class="ms-2">
                                            <strong>Notificación 2</strong>
                                            <div class="text-muted text-sm">Descripción de la notificación 2</div>
                                        </div>
                                    </li>
                                    <li class="dropdown-item d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center"
                                            style="width: 40px; height: 40px;">
                                            <span class="fs-5">T</span>
                                        </div>
                                        <div class="ms-2">
                                            <strong>Notificación 3</strong>
                                            <div class="text-muted text-sm">Descripción de la notificación 3</div>
                                        </div>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">Ver todas las notificaciones</a></li>
                                </ul>
                            </div>

                            <!-- Icono de Mensajes -->
                            <div class="dropdown me-3">
                                <a href="#" class="text-decoration-none position-relative" id="dropdownMessages"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi-envelope fs-4"></i>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
                                        5 <!-- Número de mensajes -->
                                        <span class="visually-hidden">unread messages</span>
                                    </span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownMessages">
                                    <li class="dropdown-item d-flex align-items-center">
                                        <!-- Foto de perfil del remitente -->
                                        <img src="https://via.placeholder.com/40" alt="Remitente 1"
                                            class="rounded-circle" width="40" height="40">
                                        <div class="ms-2">
                                            <strong>Remitente 1</strong>
                                            <div class="text-muted text-sm">Mensaje 1</div>
                                        </div>
                                    </li>
                                    <li class="dropdown-item d-flex align-items-center">
                                        <img src="https://via.placeholder.com/40" alt="Remitente 2"
                                            class="rounded-circle" width="40" height="40">
                                        <div class="ms-2">
                                            <strong>Remitente 2</strong>
                                            <div class="text-muted text-sm">Mensaje 2</div>
                                        </div>
                                    </li>
                                    <li class="dropdown-item d-flex align-items-center">
                                        <img src="https://via.placeholder.com/40" alt="Remitente 3"
                                            class="rounded-circle" width="40" height="40">
                                        <div class="ms-2">
                                            <strong>Remitente 3</strong>
                                            <div class="text-muted text-sm">Mensaje 3</div>
                                        </div>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">Ver todos los mensajes</a></li>
                                </ul>
                            </div>

                            <!-- Dropdown del Usuario -->
                            <div class="flex-shrink-0 dropdown">
                                <a href="#"
                                    class="d-flex align-items-center text-decoration-none dropdown-toggle"
                                    id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('images/default-avatar.png') }}"
                                        alt="{{ Auth::user()->name }}" width="32" height="32"
                                        class="rounded-circle me-2">
                                    <div class="d-none d-sm-block">
                                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                                            {{ Auth::user()->name }}</div>
                                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser2">
                                    <li><a class="dropdown-item"
                                            href="{{ route('profile.edit') }}">{{ __('admin.Profile') }}</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="dropdown-item">{{ __('admin.LogOut') }}</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>


                </div>
            </header>

            <div class="container-fluid">
                <div class="row">
                    <div class="col min-vh-100 admin-container p-4">
                        @yield('content')
                    </div>
                </div>
            </div>

            <footer class="admin-footer px-4 py-3 bg-white shadow-custom">
                &copy; Jarvis Bot V7.0 - {{ date('Y') }}
            </footer>
        </main>
        <div id="chat-system" class="jv-chat-system">
            <div id="new-chat-btn" class="jv-chat-new-chat-btn">+</div>
            <div id="user-list" class="jv-chat-user-list"></div>
            <div id="chat-bubbles" class="jv-chat-bubbles"></div>
        </div>
    @endif
    <script>
        // Definir variables globales para textos de confirmación
        window.appTranslations = {
            delete_confirm: "{{ __('admin.delete_confirm') }}",
            yes_confirm: "{{ __('admin.yes_confirm') }}",
            cancel_confirm: "{{ __('admin.cancel_confirm') }}",
            login_as_user_warning: "{{ __('admin.login_as_user_warning') }}",
            yes: "{{ __('admin.yes') }}",
            // Añade aquí cualquier otra variable de texto que necesites
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.1/dist/chartjs-adapter-moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="{{ asset('/js/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('/admin/admin-functions.js') }}"></script>
    @yield('javascript')
    @stack('scripts')
    @auth
        <script>
            const currentUserId = {{ Auth::id() }};
            const defaultAvatarPath = "{{ asset('images/default-avatar.png') }}";

            document.addEventListener('DOMContentLoaded', function() {
                const chatSystem = document.getElementById('chat-system');
                const newChatBtn = document.getElementById('new-chat-btn');
                const userList = document.getElementById('user-list');
                const chatBubbles = document.getElementById('chat-bubbles');

                let activeChats = [];

                newChatBtn.addEventListener('click', toggleUserList);

                function toggleUserList() {
                    if (userList.style.display === 'none' || userList.style.display === '') {
                        fetchUsers();
                        userList.style.display = 'block';
                    } else {
                        userList.style.display = 'none';
                    }
                }

                function fetchUsers() {
                    fetch('/api/users')
                        .then(response => response.json())
                        .then(users => {
                            userList.innerHTML = '';
                            users.forEach(user => {
                                if (user.id !== currentUserId) {
                                    const userItem = document.createElement('div');
                                    userItem.className = 'jv-chat-user-item';
                                    const nameParts = user.name.split(' ');
                                    const displayName = `${nameParts[0]} ${nameParts[2] || ''}`.trim();
                                    userItem.innerHTML = `
                                    <div class="jv-chat-user-avatar" style="background-image: url('${user.avatar || defaultAvatarPath}')"></div>
                                    <span>${displayName}</span>
                                `;
                                    userItem.addEventListener('click', () => startChat(user));
                                    userList.appendChild(userItem);
                                }
                            });
                        })
                        .catch(error => console.error('Error fetching users:', error));
                }

                function startChat(user) {
                    userList.style.display = 'none';
                    const existingChat = activeChats.find(chat => chat.userId === user.id);
                    if (existingChat) {
                        openChatWindow(existingChat.conversationId);
                    } else {
                        createNewConversation(user);
                    }
                }

                function createNewConversation(user) {
                    fetch('/api/conversations', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                user_id: user.id
                            })
                        })
                        .then(response => response.json())
                        .then(conversation => {
                            createChatBubble(user, conversation.id);
                            createChatWindow(user, conversation.id);
                        })
                        .catch(error => console.error('Error creating conversation:', error));
                }

                function createChatBubble(user, conversationId) {
                    const bubble = document.createElement('div');
                    bubble.className = 'jv-chat-bubble';
                    bubble.style.backgroundImage = `url('${user.avatar || defaultAvatarPath}')`;
                    bubble.addEventListener('click', () => openChatWindow(conversationId));
                    chatBubbles.appendChild(bubble);
                    activeChats.push({
                        userId: user.id,
                        conversationId,
                        bubble
                    });
                }

                function createChatWindow(user, conversationId) {
                    const chatWindow = document.createElement('div');
                    chatWindow.className = 'jv-chat-window';
                    chatWindow.setAttribute('data-conversation-id', conversationId);

                    const nameParts = user.name.split(' ');
                    const displayName = `${nameParts[0]} ${nameParts[2] || ''}`.trim();

                    chatWindow.innerHTML = `
                    <div class="jv-chat-header">
                        <img src="${user.avatar || defaultAvatarPath}" alt="${displayName}" class="jv-chat-header-avatar">
                        <span>${displayName}</span>
                        <button class="jv-chat-minimize-btn">_</button>
                        <button class="jv-chat-close-btn">×</button>
                    </div>
                    <div class="jv-chat-body"></div>
                    <div class="jv-chat-footer">
                        <textarea class="jv-chat-input" placeholder="Escribe un mensaje..."></textarea>
                        <button class="jv-chat-send-btn"><i class="fas fa-paper-plane"></i></button>
                    </div>
                `;

                    chatSystem.appendChild(chatWindow);

                    const minimizeBtn = chatWindow.querySelector('.jv-chat-minimize-btn');
                    const closeBtn = chatWindow.querySelector('.jv-chat-close-btn');
                    const input = chatWindow.querySelector('.jv-chat-input');
                    const sendBtn = chatWindow.querySelector('.jv-chat-send-btn');

                    minimizeBtn.addEventListener('click', () => minimizeChatWindow(conversationId));
                    closeBtn.addEventListener('click', () => closeChatWindow(conversationId));

                    input.addEventListener('keypress', (e) => {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            sendMessage(conversationId, input.value);
                        }
                    });

                    input.addEventListener('input', function() {
                        this.style.height = 'auto';
                        this.style.height = (this.scrollHeight) + 'px';
                    });

                    sendBtn.addEventListener('click', () => sendMessage(conversationId, input.value));

                    loadMessages(conversationId);
                    openChatWindow(conversationId);
                }

                function openChatWindow(conversationId) {
                    const chatWindow = document.querySelector(
                        `.jv-chat-window[data-conversation-id="${conversationId}"]`);
                    if (chatWindow) {
                        chatWindow.style.display = 'flex';
                    }
                }

                function minimizeChatWindow(conversationId) {
                    const chatWindow = document.querySelector(
                        `.jv-chat-window[data-conversation-id="${conversationId}"]`);
                    if (chatWindow) {
                        chatWindow.style.display = 'none';
                    }
                }

                function closeChatWindow(conversationId) {
                    const chatWindow = document.querySelector(
                        `.jv-chat-window[data-conversation-id="${conversationId}"]`);
                    if (chatWindow) {
                        chatWindow.remove();
                        const chatIndex = activeChats.findIndex(chat => chat.conversationId === conversationId);
                        if (chatIndex !== -1) {
                            activeChats[chatIndex].bubble.remove();
                            activeChats.splice(chatIndex, 1);
                        }
                    }
                }

                function loadMessages(conversationId) {
                    const chatBody = document.querySelector(
                        `.jv-chat-window[data-conversation-id="${conversationId}"] .jv-chat-body`);
                    fetch(`/api/conversations/${conversationId}/messages`)
                        .then(response => response.json())
                        .then(messages => {
                            chatBody.innerHTML = '';
                            messages.forEach(message => appendMessage(message, chatBody));
                            chatBody.scrollTop = chatBody.scrollHeight;
                        })
                        .catch(error => console.error('Error loading messages:', error));
                }

                function sendMessage(conversationId, content) {
                    if (!content.trim()) return;

                    const chatBody = document.querySelector(
                        `.jv-chat-window[data-conversation-id="${conversationId}"] .jv-chat-body`);
                    const input = document.querySelector(
                        `.jv-chat-window[data-conversation-id="${conversationId}"] .jv-chat-input`);

                    fetch(`/api/conversations/${conversationId}/messages`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                content: content.trim()
                            })
                        })
                        .then(response => response.json())
                        .then(message => {
                            appendMessage(message, chatBody);
                            input.value = '';
                            input.style.height = 'auto'; // Reset height after sending
                            chatBody.scrollTop = chatBody.scrollHeight;
                        })
                        .catch(error => console.error('Error sending message:', error));
                }

                function appendMessage(message, chatBody) {
                    const messageElement = document.createElement('div');
                    messageElement.className =
                        `jv-chat-message ${message.user_id === currentUserId ? 'sent' : 'received'}`;
                    messageElement.textContent = message.content;
                    chatBody.appendChild(messageElement);
                    chatBody.scrollTop = chatBody.scrollHeight;
                }

                // Configuración de Laravel Echo para mensajes en tiempo real
                if (typeof window.Echo !== 'undefined') {
                    window.Echo.private(`App.Models.User.${currentUserId}`)
                        .notification((notification) => {
                            if (notification.type === 'NewMessage') {
                                const conversationId = notification.conversation_id;
                                const chatBody = document.querySelector(
                                    `.jv-chat-window[data-conversation-id="${conversationId}"] .jv-chat-body`);
                                if (chatBody) {
                                    appendMessage(notification.message, chatBody);
                                    chatBody.scrollTop = chatBody.scrollHeight;
                                } else {
                                    // Si la ventana de chat no está abierta, crea una nueva
                                    createChatBubble(notification.sender, conversationId);
                                    createChatWindow(notification.sender, conversationId);
                                }
                            }
                        });
                }
            });
        </script>



    @endauth
    @if (session('success_update'))
        <script type="text/javascript">
            Swal.fire({
                title: "{{ session('success_update') }}",
                icon: "success",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif

    @if (session('unauthorized'))
        <script type="text/javascript">
            Swal.fire({
                title: "{{ trans('general.error_oops') }}",
                text: "{{ session('unauthorized') }}",
                icon: "error",
                confirmButtonText: "{{ trans('users.ok') }}"
            });
        </script>
    @endif

</body>

</html>
