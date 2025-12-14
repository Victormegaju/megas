<?php
$pageTitle = 'Meus Usuários';

$menuHtml = '
    <li class="nav-item">
        <a href="/revenda/dashboard" class="nav-link">
            <span class="nav-icon">📊</span>
            Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a href="/revenda/users" class="nav-link active">
            <span class="nav-icon">👥</span>
            Meus Usuários
        </a>
    </li>
    <li class="nav-item">
        <a href="/revenda/profile" class="nav-link">
            <span class="nav-icon">👤</span>
            Meu Perfil
        </a>
    </li>
';

ob_start();
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">👥 Meus Usuários</h2>
        <div>
            <button onclick="openCreateModal(false)" class="btn" style="margin-right: 10px;">+ Novo Usuário Regular</button>
            <button onclick="openCreateModal(true)" class="btn btn-warning">⏱️ Novo Usuário de Teste</button>
        </div>
    </div>
    
    <div id="alert-container"></div>
    
    <table id="users-table">
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Tipo</th>
                <th>Criado em</th>
                <th>Expira em</th>
                <th>Dias Restantes</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="users-tbody">
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px;">
                    Carregando...
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Create/Edit Modal -->
<div id="user-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2 class="modal-title" id="modal-title">Novo Usuário</h2>
        </div>
        
        <form id="user-form">
            <input type="hidden" id="user-id" name="id">
            <input type="hidden" id="is_test_user" name="is_test_user" value="0">
            
            <div class="form-group">
                <label for="username">Nome de Usuário</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group" id="password-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password">
                <small style="color: #666;">Deixe em branco para manter a senha atual</small>
            </div>
            
            <div class="form-group" id="duration-group">
                <label for="days">Dias de Acesso</label>
                <input type="number" id="days" name="days" value="30" min="1">
            </div>
            
            <div class="form-group" id="test-duration-group" style="display: none;">
                <label for="test_hours">Duração do Teste</label>
                <select id="test_hours" name="test_hours">
                    <option value="6">6 horas</option>
                    <option value="12">12 horas</option>
                    <option value="24" selected>24 horas</option>
                </select>
            </div>
            
            <button type="submit" class="btn" style="margin-right: 10px;">Criar Usuário</button>
            <button type="button" class="btn btn-danger" onclick="closeModal()">Cancelar</button>
        </form>
    </div>
</div>

<!-- Renew Modal -->
<div id="renew-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close" onclick="closeRenewModal()">&times;</span>
            <h2 class="modal-title">Renovar Usuário</h2>
        </div>
        
        <form id="renew-form">
            <input type="hidden" id="renew-user-id">
            
            <div class="form-group">
                <label for="renew-days">Adicionar Dias</label>
                <input type="number" id="renew-days" name="days" value="30" min="1" required>
            </div>
            
            <button type="submit" class="btn btn-success" style="margin-right: 10px;">Renovar</button>
            <button type="button" class="btn btn-danger" onclick="closeRenewModal()">Cancelar</button>
        </form>
    </div>
</div>

<script>
let users = [];
let isTestUser = false;

async function loadUsers() {
    try {
        const response = await fetch('/revenda/api/users');
        users = await response.json();
        renderUsers();
    } catch (error) {
        showAlert('Erro ao carregar usuários', 'error');
    }
}

function renderUsers() {
    const tbody = document.getElementById('users-tbody');
    
    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 40px;">Nenhum usuário encontrado</td></tr>';
        return;
    }
    
    tbody.innerHTML = users.map(user => `
        <tr>
            <td><strong>${escapeHtml(user.username)}</strong></td>
            <td>
                ${user.is_test_user == 1
                    ? '<span class="badge badge-warning">Teste</span>'
                    : '<span class="badge badge-success">Regular</span>'
                }
            </td>
            <td>${formatDateTime(user.created_at)}</td>
            <td>${formatDateTime(user.expiration_date)}</td>
            <td>
                ${user.remaining_days > 0 
                    ? `<span class="badge badge-success">${user.remaining_days} dias</span>`
                    : `<span class="badge badge-danger">Expirado</span>`
                }
            </td>
            <td>
                ${user.is_active == 1
                    ? '<span class="badge badge-success">✓ Ativo</span>'
                    : '<span class="badge badge-danger">✗ Suspenso</span>'
                }
            </td>
            <td>
                ${user.is_active == 1 
                    ? `<button onclick="suspendUser(${user.id})" class="btn btn-sm btn-warning">Suspender</button>`
                    : `<button onclick="activateUser(${user.id})" class="btn btn-sm btn-success">Ativar</button>`
                }
                <button onclick="renewUser(${user.id})" class="btn btn-sm btn-success">Renovar</button>
                <button onclick="deleteUser(${user.id})" class="btn btn-sm btn-danger">Excluir</button>
            </td>
        </tr>
    `).join('');
}

function openCreateModal(isTest) {
    isTestUser = isTest;
    document.getElementById('modal-title').textContent = isTest ? 'Novo Usuário de Teste' : 'Novo Usuário Regular';
    document.getElementById('user-form').reset();
    document.getElementById('user-id').value = '';
    document.getElementById('is_test_user').value = isTest ? '1' : '0';
    document.getElementById('password').required = true;
    document.getElementById('password-group').querySelector('small').style.display = 'none';
    
    if (isTest) {
        document.getElementById('duration-group').style.display = 'none';
        document.getElementById('test-duration-group').style.display = 'block';
    } else {
        document.getElementById('duration-group').style.display = 'block';
        document.getElementById('test-duration-group').style.display = 'none';
    }
    
    document.getElementById('user-modal').classList.add('show');
}

function closeModal() {
    document.getElementById('user-modal').classList.remove('show');
}

function renewUser(id) {
    document.getElementById('renew-user-id').value = id;
    document.getElementById('renew-modal').classList.add('show');
}

function closeRenewModal() {
    document.getElementById('renew-modal').classList.remove('show');
}

document.getElementById('user-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        username: document.getElementById('username').value,
        password: document.getElementById('password').value,
        is_test_user: parseInt(document.getElementById('is_test_user').value)
    };
    
    if (formData.is_test_user) {
        formData.test_hours = parseInt(document.getElementById('test_hours').value);
    } else {
        formData.days = parseInt(document.getElementById('days').value);
    }
    
    try {
        const response = await fetch('/revenda/api/users', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Usuário criado com sucesso!', 'success');
            closeModal();
            loadUsers();
        } else {
            showAlert(data.error || 'Erro ao criar usuário', 'error');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'error');
    }
});

document.getElementById('renew-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const userId = document.getElementById('renew-user-id').value;
    const days = parseInt(document.getElementById('renew-days').value);
    
    try {
        const response = await fetch(`/revenda/api/users/${userId}/renew`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ days })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Usuário renovado com sucesso!', 'success');
            closeRenewModal();
            loadUsers();
        } else {
            showAlert(data.error || 'Erro ao renovar usuário', 'error');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'error');
    }
});

async function suspendUser(id) {
    if (!confirm('Deseja realmente suspender este usuário?')) return;
    
    try {
        const response = await fetch(`/revenda/api/users/${id}/suspend`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Usuário suspenso com sucesso!', 'success');
            loadUsers();
        } else {
            showAlert(data.error || 'Erro ao suspender usuário', 'error');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'error');
    }
}

async function activateUser(id) {
    try {
        const response = await fetch(`/revenda/api/users/${id}/activate`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Usuário ativado com sucesso!', 'success');
            loadUsers();
        } else {
            showAlert(data.error || 'Erro ao ativar usuário', 'error');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'error');
    }
}

async function deleteUser(id) {
    if (!confirm('Deseja realmente excluir este usuário? Esta ação não pode ser desfeita.')) return;
    
    try {
        const response = await fetch(`/revenda/api/users/${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Usuário excluído com sucesso!', 'success');
            loadUsers();
        } else {
            showAlert(data.error || 'Erro ao excluir usuário', 'error');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'error');
    }
}

function showAlert(message, type) {
    const alertClass = type === 'error' ? 'alert-error' : 'alert-success';
    const alertContainer = document.getElementById('alert-container');
    alertContainer.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
    setTimeout(() => {
        alertContainer.innerHTML = '';
    }, 5000);
}

function formatDateTime(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load users on page load
loadUsers();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
