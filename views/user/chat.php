<?php
$pageTitle = 'Chat';

$menuHtml = '
    <li class="nav-item">
        <a href="/chat" class="nav-link active">
            <span class="nav-icon">💬</span>
            Chat
        </a>
    </li>
    <li class="nav-item">
        <a href="/profile" class="nav-link">
            <span class="nav-icon">👤</span>
            Perfil
        </a>
    </li>
';

ob_start();
?>

<style>
.chat-container {
    display: flex;
    flex-direction: column;
    height: calc(100vh - 200px);
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    overflow: hidden;
}

.chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.message {
    display: flex;
    gap: 10px;
    max-width: 80%;
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.message.user {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.message.user .message-avatar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.message.assistant .message-avatar {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.message-content {
    background: #f8fafc;
    padding: 12px 16px;
    border-radius: 12px;
    line-height: 1.6;
    word-wrap: break-word;
}

.message.user .message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.message-image {
    max-width: 300px;
    border-radius: 8px;
    margin-top: 10px;
}

.chat-input-container {
    padding: 20px;
    border-top: 2px solid #f0f0f0;
    background: white;
}

.image-preview-container {
    margin-bottom: 10px;
    position: relative;
    display: inline-block;
}

.image-preview {
    max-width: 150px;
    max-height: 150px;
    border-radius: 8px;
    border: 2px solid #e5e7eb;
}

.remove-image {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #ef4444;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
}

.chat-input-wrapper {
    display: flex;
    gap: 10px;
    align-items: flex-end;
}

.chat-input {
    flex: 1;
    padding: 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    resize: vertical;
    min-height: 50px;
    max-height: 150px;
    font-family: inherit;
    font-size: 14px;
}

.chat-input:focus {
    outline: none;
    border-color: #667eea;
}

.chat-actions {
    display: flex;
    gap: 10px;
}

.icon-btn {
    background: #f8fafc;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 20px;
    transition: all 0.3s;
}

.icon-btn:hover {
    background: #e5e7eb;
}

.send-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}

.send-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.typing-indicator {
    display: none;
    align-items: center;
    gap: 5px;
    padding: 10px;
}

.typing-indicator.show {
    display: flex;
}

.typing-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #667eea;
    animation: typing 1.4s infinite;
}

.typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-10px); }
}

.empty-state {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #666;
}

.empty-state-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.empty-state-text {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 10px;
}

.empty-state-subtext {
    font-size: 14px;
    color: #999;
}
</style>

<div class="chat-container">
    <div class="chat-header">
        <div>
            <h2 style="margin-bottom: 5px;">💬 Chat AI</h2>
            <p style="font-size: 14px; opacity: 0.9;">Converse com a inteligência artificial</p>
        </div>
        <button onclick="clearHistory()" class="btn btn-danger btn-sm">🗑️ Limpar Histórico</button>
    </div>
    
    <div class="chat-messages" id="chat-messages">
        <div class="empty-state" id="empty-state">
            <div class="empty-state-icon">🤖</div>
            <div class="empty-state-text">Olá! Como posso ajudar você hoje?</div>
            <div class="empty-state-subtext">Envie uma mensagem ou uma imagem para começar</div>
        </div>
    </div>
    
    <div class="chat-input-container">
        <div class="image-preview-container" id="image-preview-container" style="display: none;">
            <img id="image-preview" class="image-preview" src="" alt="Preview">
            <button class="remove-image" onclick="removeImage()">×</button>
        </div>
        
        <div class="chat-input-wrapper">
            <textarea 
                id="chat-input" 
                class="chat-input" 
                placeholder="Digite sua mensagem..."
                rows="1"></textarea>
            
            <div class="chat-actions">
                <label class="icon-btn" title="Anexar imagem">
                    📎
                    <input type="file" id="image-input" accept="image/*" style="display: none;" onchange="handleImageSelect(event)">
                </label>
                
                <button id="send-btn" class="icon-btn send-btn" onclick="sendMessage()" title="Enviar">
                    ➤
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedImage = null;
let isLoading = false;

// Load chat history on page load
loadChatHistory();

async function loadChatHistory() {
    try {
        const response = await fetch('/api/chat/history');
        const data = await response.json();
        
        if (data.messages && data.messages.length > 0) {
            document.getElementById('empty-state').style.display = 'none';
            data.messages.forEach(msg => {
                appendMessage(msg.message_type, msg.content, false);
            });
            scrollToBottom();
        }
    } catch (error) {
        console.error('Error loading chat history:', error);
    }
}

function handleImageSelect(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (!file.type.startsWith('image/')) {
        alert('Por favor, selecione uma imagem válida');
        return;
    }
    
    if (file.size > 2 * 1024 * 1024) {
        alert('A imagem deve ter no máximo 2MB');
        return;
    }
    
    const reader = new FileReader();
    reader.onload = (e) => {
        selectedImage = e.target.result;
        document.getElementById('image-preview').src = selectedImage;
        document.getElementById('image-preview-container').style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    selectedImage = null;
    document.getElementById('image-preview-container').style.display = 'none';
    document.getElementById('image-input').value = '';
}

async function sendMessage() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    
    if (!message && !selectedImage) return;
    if (isLoading) return;
    
    // Hide empty state
    document.getElementById('empty-state').style.display = 'none';
    
    // Display user message
    appendMessage('user', message, true);
    
    // Clear input
    input.value = '';
    
    // Show typing indicator
    const typingIndicator = document.createElement('div');
    typingIndicator.className = 'typing-indicator show';
    typingIndicator.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';
    document.getElementById('chat-messages').appendChild(typingIndicator);
    scrollToBottom();
    
    isLoading = true;
    document.getElementById('send-btn').disabled = true;
    
    try {
        const response = await fetch('/api/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                message: message,
                image: selectedImage
            })
        });
        
        const data = await response.json();
        
        // Remove typing indicator
        typingIndicator.remove();
        
        if (data.success) {
            appendMessage('assistant', data.message, true);
        } else {
            appendMessage('assistant', 'Desculpe, ocorreu um erro: ' + (data.error || 'Erro desconhecido'), true);
        }
    } catch (error) {
        typingIndicator.remove();
        appendMessage('assistant', 'Erro de conexão. Tente novamente.', true);
    }
    
    // Remove image after sending
    removeImage();
    
    isLoading = false;
    document.getElementById('send-btn').disabled = false;
}

function appendMessage(type, content, animate) {
    const messagesContainer = document.getElementById('chat-messages');
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    if (!animate) messageDiv.style.animation = 'none';
    
    const avatar = document.createElement('div');
    avatar.className = 'message-avatar';
    avatar.textContent = type === 'user' ? '👤' : '🤖';
    
    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    contentDiv.textContent = content;
    
    messageDiv.appendChild(avatar);
    messageDiv.appendChild(contentDiv);
    
    messagesContainer.appendChild(messageDiv);
    scrollToBottom();
}

function scrollToBottom() {
    const messagesContainer = document.getElementById('chat-messages');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

async function clearHistory() {
    if (!confirm('Deseja realmente limpar todo o histórico de chat?')) return;
    
    try {
        const response = await fetch('/api/chat/clear', {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('chat-messages').innerHTML = `
                <div class="empty-state" id="empty-state">
                    <div class="empty-state-icon">🤖</div>
                    <div class="empty-state-text">Histórico limpo! Como posso ajudar você?</div>
                    <div class="empty-state-subtext">Envie uma mensagem ou uma imagem para começar</div>
                </div>
            `;
        }
    } catch (error) {
        alert('Erro ao limpar histórico');
    }
}

// Send message on Enter (but allow Shift+Enter for new line)
document.getElementById('chat-input').addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
});
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
