<?php
$pageTitle = 'Gerenciar Revendedores';

$menuHtml = '
    <li class="nav-item">
        <a href="/admin/dashboard" class="nav-link">
            <span class="nav-icon">📊</span>
            Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a href="/admin/users" class="nav-link">
            <span class="nav-icon">👥</span>
            Usuários
        </a>
    </li>
    <li class="nav-item">
        <a href="/admin/resellers" class="nav-link active">
            <span class="nav-icon">🏢</span>
            Revendedores
        </a>
    </li>
    <li class="nav-item">
        <a href="/admin/settings" class="nav-link">
            <span class="nav-icon">⚙️</span>
            Configurações
        </a>
    </li>
';

ob_start();
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">🏢 Gerenciar Revendedores</h2>
        <button onclick="openCreateModal()" class="btn">+ Novo Revendedor</button>
    </div>
    
    <div id="alert-container"></div>
    
    <table id="resellers-table">
        <thead>
            <tr>
                <th>Revendedor</th>
                <th>Criado em</th>
                <th>Expira em</th>
                <th>Dias Restantes</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="resellers-tbody">
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px;">
                    Carregando...
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Create/Edit Modal -->
<div id="reseller-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close" onclick="closeModal()">&times;</span>
            <h2 class="modal-title" id="modal-title">Novo Revendedor</h2>
        </div>
        
        <form id="reseller-form">
            <input type="hidden" id="reseller-id" name="id">
            
            <div class="form-group">
                <label for="username">Nome de Usuário</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group" id="password-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password">
                <small style="color: #666;">Deixe em branco para manter a senha atual</small>
            </div>
            
            <div class="form-group">
                <label for="days">Dias de Acesso</label>
                <input type="number" id="days" name="days" value="30" min="1">
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" id="is_active" name="is_active" checked>
                    Conta Ativa
                </label>
            </div>
            
            <button type="submit" class="btn" style="margin-right: 10px;">Salvar</button>
            <button type="button" class="btn btn-danger" onclick="closeModal()">Cancelar</button>
        </form>
    </div>
</div>

<!-- Renew Modal -->
<div id="renew-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-close" onclick="closeRenewModal()">&times;</span>
            <h2 class="modal-title">Renovar Revendedor</h2>
        </div>
        
        <form id="renew-form">
            <input type="hidden" id="renew-reseller-id">
            
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
let resellers = [];
let editingResellerId = null;

async function loadResellers() {
    try {
        const response = await fetch('/admin/api/resellers');
        resellers = await response.json();
        renderResellers();
    } catch (error) {
        showAlert('Erro ao carregar revendedores', 'error');
    }
}

function renderResellers() {
    const tbody = document.getElementById('resellers-tbody');
    
    if (resellers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 40px;">Nenhum revendedor encontrado</td></tr>';
        return;
    }
    
    tbody.innerHTML = resellers.map(reseller => `
        <tr>
            <td><strong>${escapeHtml(reseller.username)}</strong></td>
            <td>${formatDate(reseller.created_at)}</td>
            <td>${formatDate(reseller.expiration_date)}</td>
            <td>
                ${reseller.remaining_days > 0 
                    ? `<span class="badge badge-success">${reseller.remaining_days} dias</span>`
                    : `<span class="badge badge-danger">Expirado</span>`
                }
            </td>
            <td>
                ${reseller.is_active == 1
                    ? '<span class="badge badge-success">✓ Ativo</span>'
                    : '<span class="badge badge-danger">✗ Suspenso</span>'
                }
            </td>
            <td>
                <button onclick="editReseller(${reseller.id})" class="btn btn-sm btn-info">Editar</button>
                ${reseller.is_active == 1 
                    ? `<button onclick="suspendReseller(${reseller.id})" class="btn btn-sm btn-warning">Suspender</button>`
                    : `<button onclick="activateReseller(${reseller.id})" class="btn btn-sm btn-success">Ativar</button>`
                }
                <button onclick="renewReseller(${reseller.id})" class="btn btn-sm btn-success">Renovar</button>
                <button onclick="deleteReseller(${reseller.id})" class="btn btn-sm btn-danger">Excluir</button>
            </td>
        </tr>
    `).join('');
}

function openCreateModal() {
    editingResellerId = null;
    document.getElementById('modal-title').textContent = 'Novo Revendedor';
    document.getElementById('reseller-form').reset();
    document.getElementById('reseller-id').value = '';
    document.getElementById('password').required = true;
    document.getElementById('password-group').querySelector('small').style.display = 'none';
    document.getElementById('reseller-modal').classList.add('show');
}

function editReseller(id) {
    const reseller = resellers.find(r => r.id === id);
    if (!reseller) return;
    
    editingResellerId = id;
    document.getElementById('modal-title').textContent = 'Editar Revendedor';
    document.getElementById('reseller-id').value = reseller.id;
    document.getElementById('username').value = reseller.username;
    document.getElementById('password').value = '';
    document.getElementById('password').required = false;
    document.getElementById('password-group').querySelector('small').style.display = 'block';
    document.getElementById('is_active').checked = reseller.is_active == 1;
    document.getElementById('reseller-modal').classList.add('show');
}

function closeModal() {
    document.getElementById('reseller-modal').classList.remove('show');
}

function renewReseller(id) {
    document.getElementById('renew-reseller-id').value = id;
    document.getElementById('renew-modal').classList.add('show');
}

function closeRenewModal() {
    document.getElementById('renew-modal').classList.remove('show');
}

document.getElementById('reseller-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        username: document.getElementById('username').value,
        password: document.getElementById('password').value,
        role: 'revenda',
        days: parseInt(document.getElementById('days').value),
        is_active: document.getElementById('is_active').checked ? 1 : 0
    };
    
    try {
        let response;
        if (editingResellerId) {
            response = await fetch(`/admin/api/users/${editingResellerId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
        } else {
            response = await fetch('/admin/api/users', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
        }
        
        const data = await response.json();
        
        if (data.success) {
            showAlert(editingResellerId ? 'Revendedor atualizado com sucesso!' : 'Revendedor criado com sucesso!', 'success');
            closeModal();
            loadResellers();
        } else {
            showAlert(data.error || 'Erro ao salvar revendedor', 'error');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'error');
    }
});

document.getElementById('renew-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const resellerId = document.getElementById('renew-reseller-id').value;
    const days = parseInt(document.getElementById('renew-days').value);
    
    try {
        const response = await fetch(`/admin/api/users/${resellerId}/renew`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ days })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Revendedor renovado com sucesso!', 'success');
            closeRenewModal();
            loadResellers();
        } else {
            showAlert(data.error || 'Erro ao renovar revendedor', 'error');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'error');
    }
});

async function suspendReseller(id) {
    if (!confirm('Deseja realmente suspender este revendedor?')) return;
    
    try {
        const response = await fetch(`/admin/api/users/${id}/suspend`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Revendedor suspenso com sucesso!', 'success');
            loadResellers();
        } else {
            showAlert(data.error || 'Erro ao suspender revendedor', 'error');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'error');
    }
}

async function activateReseller(id) {
    try {
        const response = await fetch(`/admin/api/users/${id}/activate`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Revendedor ativado com sucesso!', 'success');
            loadResellers();
        } else {
            showAlert(data.error || 'Erro ao ativar revendedor', 'error');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'error');
    }
}

async function deleteReseller(id) {
    if (!confirm('Deseja realmente excluir este revendedor? Esta ação não pode ser desfeita.')) return;
    
    try {
        const response = await fetch(`/admin/api/users/${id}`, {
            method: 'DELETE'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Revendedor excluído com sucesso!', 'success');
            loadResellers();
        } else {
            showAlert(data.error || 'Erro ao excluir revendedor', 'error');
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

function formatDate(dateStr) {
    const date = new Date(dateStr);
    return date.toLocaleDateString('pt-BR');
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Load resellers on page load
loadResellers();
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
