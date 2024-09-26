<!-- Chat System -->
<div id="chat-system">
    <div id="chat-bubbles"></div>
    <div id="active-chats"></div>
    <button id="new-chat-btn" class="new-chat-btn">
        <i class="bi bi-plus-lg"></i>
    </button>
</div>

<style>
    #chat-system {
        position: fixed;
        bottom: 0;
        right: 0;
        width: 300px;
        z-index: 1000;
    }

    .chat-bubble {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #007bff;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        z-index: 1001;
    }

    .chat-window {
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 300px;
        height: 400px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: white;
        display: flex;
        flex-direction: column;
        z-index: 1002;
    }

    .chat-header {
        background-color: #007bff;
        color: white;
        padding: 10px;
        display: flex;
        justify-content: space-between;
    }

    .chat-messages {
        flex-grow: 1;
        overflow-y: auto;
        padding: 10px;
    }

    .chat-input {
        padding: 10px;
        border-top: 1px solid #ccc;
    }

    .chat-input input {
        width: 100%;
        padding: 5px;
    }

    .new-chat-btn {
        position: fixed;
        bottom: 20px;
        left: 20px;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #28a745;
        color: white;
        border: none;
        font-size: 24px;
        cursor: pointer;
        z-index: 1001;
    }
</style>

<script>
class ChatSystem {
    constructor() {
        this.conversations = [];
        this.activeConversation = null;
        this.initEventListeners();
    }

    initEventListeners() {
        document.getElementById('new-chat-btn').addEventListener('click', () => this.addConversation());
    }

    addConversation() {
        const newConversation = {
            id: Date.now(),
            name: `Chat ${this.conversations.length + 1}`
        };
        this.conversations.push(newConversation);
        this.renderChatBubble(newConversation);
        this.setActiveConversation(newConversation);
    }

    renderChatBubble(conversation) {
        const bubble = document.createElement('div');
        bubble.className = 'chat-bubble';
        bubble.innerHTML = '<i class="bi bi-chat-dots"></i>';
        bubble.addEventListener('click', () => this.setActiveConversation(conversation));
        document.getElementById('chat-bubbles').appendChild(bubble);
    }

    setActiveConversation(conversation) {
        if (this.activeConversation) {
            this.closeChatWindow(this.activeConversation);
        }
        this.activeConversation = conversation;
        this.renderChatWindow(conversation);
    }

    renderChatWindow(conversation) {
        const chatWindow = document.createElement('div');
        chatWindow.className = 'chat-window';
        chatWindow.innerHTML = `
            <div class="chat-header">
                <span>${conversation.name}</span>
                <button class="close-btn"><i class="bi bi-x"></i></button>
            </div>
            <div class="chat-messages"></div>
            <div class="chat-input">
                <input type="text" placeholder="Type a message...">
            </div>
        `;
        chatWindow.querySelector('.close-btn').addEventListener('click', () => this.closeChatWindow(conversation));
        document.getElementById('active-chats').appendChild(chatWindow);
    }

    closeChatWindow(conversation) {
        const chatWindows = document.querySelectorAll('.chat-window');
        chatWindows.forEach(window => {
            if (window.querySelector('.chat-header span').textContent === conversation.name) {
                window.remove();
            }
        });
        this.activeConversation = null;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.chatSystem = new ChatSystem();
});
</script>
