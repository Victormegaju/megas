<?php
$pageTitle = 'Dashboard Revendedor';

$menuHtml = '
    <li class="nav-item">
        <a href="/revenda/dashboard" class="nav-link active">
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
        <a href="/revenda/profile" class="nav-link">
            <span class="nav-icon">👤</span>
            Meu Perfil
        </a>
    </li>
';

// Get statistics
$myUsers = $user->getCreatedUsers();
$totalUsers = count($myUsers);
$activeUsers = array_filter($myUsers, function($u) { return $u->isActive(); });
$testUsers = array_filter($myUsers, function($u) { return $u->is_test_user; });

ob_start();
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Total de Usuários</div>
        <div style="font-size: 36px; font-weight: 700;"><?php echo $totalUsers; ?></div>
    </div>
    
    <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Usuários Ativos</div>
        <div style="font-size: 36px; font-weight: 700;"><?php echo count($activeUsers); ?></div>
    </div>
    
    <div class="card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Usuários de Teste</div>
        <div style="font-size: 36px; font-weight: 700;"><?php echo count($testUsers); ?></div>
    </div>
    
    <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Minha Validade</div>
        <div style="font-size: 36px; font-weight: 700;"><?php echo $user->getRemainingDays(); ?> dias</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">👋 Bem-vindo, <?php echo htmlspecialchars($user->username); ?>!</h2>
    </div>
    
    <div style="color: #666; line-height: 1.8;">
        <p style="margin-bottom: 15px;">
            Como revendedor, você pode gerenciar seus próprios usuários:
        </p>
        <ul style="list-style: none; padding-left: 0;">
            <li style="margin-bottom: 10px;">
                <strong>👥 Meus Usuários:</strong> Criar, editar, suspender e renovar usuários criados por você.
            </li>
            <li style="margin-bottom: 10px;">
                <strong>⏱️ Usuários de Teste:</strong> Criar usuários com acesso temporário de 6h, 12h ou 24h.
            </li>
            <li style="margin-bottom: 10px;">
                <strong>👤 Meu Perfil:</strong> Ver informações da sua conta, dias restantes e renovar seu acesso.
            </li>
        </ul>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">⚠️ Status da Minha Conta</h2>
    </div>
    
    <div style="padding: 20px; background: <?php echo $user->getRemainingDays() < 7 ? '#fef3c7' : '#d1fae5'; ?>; border-radius: 8px;">
        <div style="margin-bottom: 10px;">
            <strong>Expira em:</strong> <?php echo date('d/m/Y H:i', strtotime($user->expiration_date)); ?>
        </div>
        <div style="margin-bottom: 10px;">
            <strong>Dias restantes:</strong> <?php echo $user->getRemainingDays(); ?> dias
        </div>
        
        <?php if ($user->getRemainingDays() < 7): ?>
        <div style="margin-top: 15px;">
            <strong style="color: #92400e;">⚠️ Sua conta está próxima do vencimento!</strong><br>
            <a href="/revenda/profile" class="btn btn-warning" style="margin-top: 10px;">Renovar Agora</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">📋 Usuários Recentes</h2>
        <a href="/revenda/users" class="btn btn-sm">Ver Todos</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Usuário</th>
                <th>Tipo</th>
                <th>Criado em</th>
                <th>Expira em</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $recentUsers = array_slice($myUsers, 0, 5);
            foreach ($recentUsers as $u):
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($u->username); ?></strong></td>
                <td>
                    <?php if ($u->is_test_user): ?>
                        <span class="badge badge-warning">Teste</span>
                    <?php else: ?>
                        <span class="badge badge-success">Regular</span>
                    <?php endif; ?>
                </td>
                <td><?php echo date('d/m/Y H:i', strtotime($u->created_at)); ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($u->expiration_date)); ?></td>
                <td>
                    <?php if ($u->isActive()): ?>
                        <span class="badge badge-success">✓ Ativo</span>
                    <?php else: ?>
                        <span class="badge badge-danger">✗ Inativo</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            
            <?php if (count($recentUsers) === 0): ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #666;">
                    Você ainda não criou nenhum usuário
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
