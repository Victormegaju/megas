<?php
$pageTitle = 'Dashboard Admin';

$menuHtml = '
    <li class="nav-item">
        <a href="/admin/dashboard" class="nav-link active">
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
        <a href="/admin/settings" class="nav-link">
            <span class="nav-icon">⚙️</span>
            Configurações
        </a>
    </li>
';

// Get statistics
$totalUsers = count(User::getAll($db, 'usuario'));
$totalResellers = count(User::getAll($db, 'revenda'));
$allUsers = User::getAll($db);
$activeUsers = array_filter($allUsers, function($u) { return $u->isActive(); });
$expiredUsers = count($allUsers) - count($activeUsers);

ob_start();
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
    <div class="card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Total de Usuários</div>
        <div style="font-size: 36px; font-weight: 700;"><?php echo $totalUsers; ?></div>
    </div>
    
    <div class="card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Revendedores</div>
        <div style="font-size: 36px; font-weight: 700;"><?php echo $totalResellers; ?></div>
    </div>
    
    <div class="card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Usuários Ativos</div>
        <div style="font-size: 36px; font-weight: 700;"><?php echo count($activeUsers); ?></div>
    </div>
    
    <div class="card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Expirados</div>
        <div style="font-size: 36px; font-weight: 700;"><?php echo $expiredUsers; ?></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">👋 Bem-vindo, Administrador!</h2>
    </div>
    
    <div style="color: #666; line-height: 1.8;">
        <p style="margin-bottom: 15px;">
            Use o menu lateral para navegar pelas diferentes seções do sistema:
        </p>
        <ul style="list-style: none; padding-left: 0;">
            <li style="margin-bottom: 10px;">
                <strong>👥 Usuários:</strong> Gerenciar usuários comuns, criar novos, editar, suspender e renovar acessos.
            </li>
            <li style="margin-bottom: 10px;">
                <strong>🏢 Revendedores:</strong> Gerenciar revendedores, que podem criar e gerenciar seus próprios usuários.
            </li>
            <li style="margin-bottom: 10px;">
                <strong>⚙️ Configurações:</strong> Configurar API do Gemini, credenciais do Mercado Pago e fazer upload do logo do site.
            </li>
        </ul>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">📋 Usuários Recentes</h2>
        <a href="/admin/users" class="btn btn-sm">Ver Todos</a>
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
            $recentUsers = array_slice($allUsers, 0, 5);
            foreach ($recentUsers as $u):
                if ($u->isAdmin()) continue;
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($u->username); ?></strong></td>
                <td>
                    <?php if ($u->isRevenda()): ?>
                        <span class="badge badge-info">Revendedor</span>
                    <?php else: ?>
                        <span class="badge badge-success">Usuário</span>
                    <?php endif; ?>
                </td>
                <td><?php echo date('d/m/Y', strtotime($u->created_at)); ?></td>
                <td><?php echo date('d/m/Y', strtotime($u->expiration_date)); ?></td>
                <td>
                    <?php if ($u->isActive()): ?>
                        <span class="badge badge-success">✓ Ativo</span>
                    <?php else: ?>
                        <span class="badge badge-danger">✗ Inativo</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layout.php';
?>
