<?php
$pageTitle = 'Dashboard';

$menuHtml = '
    <li class="nav-item">
        <a href="/chat" class="nav-link">
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

<div class="card">
    <div class="card-header">
        <h2 class="card-title">👋 Bem-vindo, <?php echo htmlspecialchars($user->username); ?>!</h2>
    </div>
    
    <div style="padding: 20px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 12px;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Sua Conta Expira em</div>
                <div style="font-size: 32px; font-weight: 700;"><?php echo date('d/m/Y', strtotime($user->expiration_date)); ?></div>
            </div>
            
            <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 30px; border-radius: 12px;">
                <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Dias Restantes</div>
                <div style="font-size: 32px; font-weight: 700;"><?php echo $user->getRemainingDays(); ?> dias</div>
            </div>
        </div>
        
        <div style="background: #f8fafc; padding: 30px; border-radius: 12px; text-align: center;">
            <div style="font-size: 48px; margin-bottom: 20px;">💬</div>
            <h3 style="margin-bottom: 15px; color: #333;">Comece a Conversar!</h3>
            <p style="color: #666; margin-bottom: 25px;">
                Use nosso chat com inteligência artificial para obter respostas, análise de imagens e muito mais.
            </p>
            <a href="/chat" class="btn" style="text-decoration: none; display: inline-block;">
                Ir para o Chat
            </a>
        </div>
    </div>
</div>

<?php if ($user->getRemainingDays() < 7): ?>
<div class="card">
    <div class="card-header">
        <h2 class="card-title">⚠️ Atenção!</h2>
    </div>
    
    <div style="padding: 20px; background: #fef3c7; border-radius: 8px; margin: 20px;">
        <p style="color: #92400e; margin-bottom: 15px;">
            <strong>Sua conta está próxima do vencimento!</strong> 
            Você tem apenas <strong><?php echo $user->getRemainingDays(); ?> dias</strong> restantes.
        </p>
        <a href="/profile" class="btn btn-warning">Renovar Agora</a>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
