<?php
$pageTitle = 'Configurações';

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
        <a href="/admin/resellers" class="nav-link">
            <span class="nav-icon">🏢</span>
            Revendedores
        </a>
    </li>
    <li class="nav-item">
        <a href="/admin/settings" class="nav-link active">
            <span class="nav-icon">⚙️</span>
            Configurações
        </a>
    </li>
';

$settings = new Settings($db);
$allSettings = $settings->getAll();

ob_start();
?>

<div id="alert-container"></div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">🎨 Logo do Site</h2>
    </div>
    
    <form id="logo-form" enctype="multipart/form-data">
        <div style="margin-bottom: 20px;">
            <?php if ($settings->getSiteLogo()): ?>
                <img src="/uploads/logo/<?php echo htmlspecialchars($settings->getSiteLogo()); ?>" 
                     alt="Current Logo" 
                     style="max-width: 200px; max-height: 100px; margin-bottom: 15px; border: 2px solid #e5e7eb; border-radius: 8px; padding: 10px;">
            <?php else: ?>
                <p style="color: #666; margin-bottom: 15px;">Nenhum logo configurado</p>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="logo">Upload Novo Logo (JPG, PNG, GIF, WEBP - Máx 2MB)</label>
            <input type="file" id="logo" name="logo" accept="image/jpeg,image/png,image/gif,image/webp">
        </div>
        
        <button type="submit" class="btn">Atualizar Logo</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">🤖 Configuração do Gemini AI</h2>
    </div>
    
    <form id="gemini-form">
        <div class="form-group">
            <label for="gemini_api_key">API Key do Gemini</label>
            <input type="password" id="gemini_api_key" name="gemini_api_key" 
                   value="<?php echo htmlspecialchars($allSettings['gemini_api_key'] ?? ''); ?>"
                   placeholder="AIza...">
        </div>
        
        <div class="form-group">
            <label for="gemini_model">Modelo do Gemini</label>
            <select id="gemini_model" name="gemini_model">
                <option value="gemini-pro" <?php echo ($allSettings['gemini_model'] ?? 'gemini-pro') === 'gemini-pro' ? 'selected' : ''; ?>>gemini-pro</option>
                <option value="gemini-1.5-pro" <?php echo ($allSettings['gemini_model'] ?? '') === 'gemini-1.5-pro' ? 'selected' : ''; ?>>gemini-1.5-pro</option>
                <option value="gemini-1.5-flash" <?php echo ($allSettings['gemini_model'] ?? '') === 'gemini-1.5-flash' ? 'selected' : ''; ?>>gemini-1.5-flash</option>
            </select>
        </div>
        
        <button type="submit" class="btn">Salvar Configurações Gemini</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">💳 Configuração do Mercado Pago</h2>
    </div>
    
    <form id="mp-form">
        <div class="form-group">
            <label for="mp_access_token">Access Token</label>
            <input type="password" id="mp_access_token" name="mp_access_token" 
                   value="<?php echo htmlspecialchars($allSettings['mp_access_token'] ?? ''); ?>"
                   placeholder="APP_USR-...">
        </div>
        
        <div class="form-group">
            <label for="mp_public_key">Public Key</label>
            <input type="text" id="mp_public_key" name="mp_public_key" 
                   value="<?php echo htmlspecialchars($allSettings['mp_public_key'] ?? ''); ?>"
                   placeholder="APP_USR-...">
        </div>
        
        <div class="form-group">
            <label for="mp_webhook_signature_key">Webhook Signature Key</label>
            <input type="password" id="mp_webhook_signature_key" name="mp_webhook_signature_key" 
                   value="<?php echo htmlspecialchars($allSettings['mp_webhook_signature_key'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" id="mp_payments_enabled" name="mp_payments_enabled" 
                       <?php echo ($allSettings['mp_payments_enabled'] ?? '0') === '1' ? 'checked' : ''; ?>>
                Habilitar Pagamentos
            </label>
        </div>
        
        <div class="alert alert-info">
            <strong>URL do Webhook:</strong><br>
            <code><?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>/appeal/webhooks/mercadopago.php</code><br>
            <small>Configure esta URL no painel do Mercado Pago para receber notificações de pagamento.</small>
        </div>
        
        <button type="submit" class="btn">Salvar Configurações Mercado Pago</button>
    </form>
</div>

<script>
document.getElementById('logo-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData();
    const logoFile = document.getElementById('logo').files[0];
    
    if (!logoFile) {
        showAlert('Selecione um arquivo de logo', 'error');
        return;
    }
    
    formData.append('logo', logoFile);
    
    try {
        const response = await fetch('/admin/settings', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Logo atualizado com sucesso! Recarregando...', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.error || 'Erro ao atualizar logo', 'error');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'error');
    }
});

document.getElementById('gemini-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('gemini_api_key', document.getElementById('gemini_api_key').value);
    formData.append('gemini_model', document.getElementById('gemini_model').value);
    
    try {
        const response = await fetch('/admin/settings', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Configurações do Gemini salvas com sucesso!', 'success');
        } else {
            showAlert(data.error || 'Erro ao salvar configurações', 'error');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'error');
    }
});

document.getElementById('mp-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData();
    formData.append('mp_access_token', document.getElementById('mp_access_token').value);
    formData.append('mp_public_key', document.getElementById('mp_public_key').value);
    formData.append('mp_webhook_signature_key', document.getElementById('mp_webhook_signature_key').value);
    formData.append('mp_payments_enabled', document.getElementById('mp_payments_enabled').checked ? '1' : '0');
    
    try {
        const response = await fetch('/admin/settings', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Configurações do Mercado Pago salvas com sucesso!', 'success');
        } else {
            showAlert(data.error || 'Erro ao salvar configurações', 'error');
        }
    } catch (error) {
        showAlert('Erro de conexão', 'error');
    }
});

function showAlert(message, type) {
    const alertClass = type === 'error' ? 'alert-error' : 'alert-success';
    const alertContainer = document.getElementById('alert-container');
    alertContainer.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
    setTimeout(() => {
        alertContainer.innerHTML = '';
    }, 5000);
}
</script>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
