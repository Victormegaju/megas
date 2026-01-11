<?php
$pageTitle = 'Meu Perfil';

$menuHtml = '
    <li class="nav-item">
        <a href="/revenda/dashboard" class="nav-link">
            <span class="nav-icon">📊</span>
            Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a href="/revenda/users" class="nav-link">
            <span class="nav-icon">👥</span>
            Meus Usuários
        </a>
    </li>
    <li class="nav-item">
        <a href="/revenda/profile" class="nav-link active">
            <span class="nav-icon">👤</span>
            Meu Perfil
        </a>
    </li>
';

$settings = new Settings($db);
$paymentsEnabled = $settings->isPaymentsEnabled();

ob_start();
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">👤 Informações da Conta</h2>
    </div>
    
    <div style="padding: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div>
                <div style="color: #666; margin-bottom: 5px;">Nome de Usuário</div>
                <div style="font-size: 20px; font-weight: 600;"><?php echo htmlspecialchars($user->username); ?></div>
            </div>
            
            <div>
                <div style="color: #666; margin-bottom: 5px;">Tipo de Conta</div>
                <div style="font-size: 20px; font-weight: 600;">
                    <span class="badge badge-info" style="font-size: 16px;">Revendedor</span>
                </div>
            </div>
            
            <div>
                <div style="color: #666; margin-bottom: 5px;">Data de Expiração</div>
                <div style="font-size: 20px; font-weight: 600;">
                    <?php echo date('d/m/Y H:i', strtotime($user->expiration_date)); ?>
                </div>
            </div>
            
            <div>
                <div style="color: #666; margin-bottom: 5px;">Dias Restantes</div>
                <div style="font-size: 20px; font-weight: 600;">
                    <?php if ($user->getRemainingDays() > 7): ?>
                        <span style="color: #059669;"><?php echo $user->getRemainingDays(); ?> dias</span>
                    <?php elseif ($user->getRemainingDays() > 0): ?>
                        <span style="color: #d97706;">⚠️ <?php echo $user->getRemainingDays(); ?> dias</span>
                    <?php else: ?>
                        <span style="color: #dc2626;">❌ Expirado</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($paymentsEnabled): ?>
<div class="card">
    <div class="card-header">
        <h2 class="card-title">💳 Renovar Acesso</h2>
    </div>
    
    <div style="padding: 20px;">
        <p style="color: #666; margin-bottom: 20px;">
            Escolha um plano para renovar ou adicionar créditos à sua conta:
        </p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; text-align: center;">
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 10px;">30 Dias</div>
                <div style="font-size: 32px; font-weight: 700; color: #667eea; margin-bottom: 15px;">R$ 50,00</div>
                <button onclick="initiatePayment(30, 50)" class="btn" style="width: 100%;">Escolher</button>
            </div>
            
            <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; text-align: center;">
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 10px;">60 Dias</div>
                <div style="font-size: 32px; font-weight: 700; color: #667eea; margin-bottom: 15px;">R$ 90,00</div>
                <button onclick="initiatePayment(60, 90)" class="btn" style="width: 100%;">Escolher</button>
            </div>
            
            <div style="border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; text-align: center;">
                <div style="font-size: 18px; font-weight: 600; margin-bottom: 10px;">90 Dias</div>
                <div style="font-size: 32px; font-weight: 700; color: #667eea; margin-bottom: 15px;">R$ 120,00</div>
                <button onclick="initiatePayment(90, 120)" class="btn" style="width: 100%;">Escolher</button>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="card-header">
        <h2 class="card-title">💳 Renovar Acesso</h2>
    </div>
    
    <div class="alert alert-info">
        Entre em contato com o administrador para renovar seu acesso.
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">🔒 Alterar Senha</h2>
    </div>
    
    <form id="password-form" style="max-width: 500px;">
        <div class="form-group">
            <label for="current_password">Senha Atual</label>
            <input type="password" id="current_password" name="current_password" required>
        </div>
        
        <div class="form-group">
            <label for="new_password">Nova Senha</label>
            <input type="password" id="new_password" name="new_password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirmar Nova Senha</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <button type="submit" class="btn">Alterar Senha</button>
    </form>
    
    <div id="password-alert" style="margin-top: 20px;"></div>
</div>

<script>
async function initiatePayment(days, amount) {
    if (!confirm(`Deseja renovar por ${days} dias por R$ ${amount.toFixed(2)}?`)) return;
    
    try {
        const response = await fetch('/payment/create', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                type: 'renewal',
                days: days,
                amount: amount
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Redirect to payment page
            window.location.href = '/payment/checkout?id=' + data.payment_id;
        } else {
            alert(data.error || 'Erro ao iniciar pagamento');
        }
    } catch (error) {
        alert('Erro de conexão');
    }
}

document.getElementById('password-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const currentPassword = document.getElementById('current_password').value;
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        showPasswordAlert('As senhas não coincidem', 'error');
        return;
    }
    
    if (newPassword.length < 6) {
        showPasswordAlert('A senha deve ter pelo menos 6 caracteres', 'error');
        return;
    }
    
    try {
        const response = await fetch('/api/change-password', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                current_password: currentPassword,
                new_password: newPassword
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showPasswordAlert('Senha alterada com sucesso!', 'success');
            document.getElementById('password-form').reset();
        } else {
            showPasswordAlert(data.error || 'Erro ao alterar senha', 'error');
        }
    } catch (error) {
        showPasswordAlert('Erro de conexão', 'error');
    }
});

function showPasswordAlert(message, type) {
    const alertClass = type === 'error' ? 'alert-error' : 'alert-success';
    const alertContainer = document.getElementById('password-alert');
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
