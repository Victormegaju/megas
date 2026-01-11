<?php
$config = require __DIR__ . '/../config.php';
$db = Database::getInstance($config['db']);
$settings = new Settings($db);
$logo = $settings->getSiteLogo();
$expired = $_SESSION['expired'] ?? false;
unset($_SESSION['expired']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Megas Chat</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 420px;
            width: 100%;
            overflow: hidden;
        }
        .logo-section {
            padding: 40px 40px 20px;
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .logo {
            max-width: 150px;
            max-height: 80px;
            margin-bottom: 20px;
        }
        .logo-text {
            color: white;
            font-size: 32px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .form-section {
            padding: 40px;
        }
        h2 {
            color: #333;
            margin-bottom: 10px;
            font-size: 24px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            color: #333;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-error {
            background: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }
        .alert-warning {
            background: #ffe;
            color: #aa0;
            border: 1px solid #ffc;
        }
        .loader {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        .modal.show {
            display: flex;
        }
        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }
        .modal-icon {
            font-size: 50px;
            margin-bottom: 20px;
        }
        .modal h3 {
            color: #333;
            margin-bottom: 15px;
        }
        .modal p {
            color: #666;
            margin-bottom: 25px;
        }
        .modal .btn {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-section">
            <?php if ($logo && file_exists(__DIR__ . '/../uploads/logo/' . $logo)): ?>
                <img src="/uploads/logo/<?php echo htmlspecialchars($logo); ?>" alt="Logo" class="logo">
            <?php else: ?>
                <div class="logo-text">💬 Megas Chat</div>
            <?php endif; ?>
        </div>
        
        <div class="form-section">
            <h2>Bem-vindo</h2>
            <p>Faça login para continuar</p>
            
            <div id="alert-container"></div>
            
            <form id="login-form">
                <div class="form-group">
                    <label for="username">Usuário</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn" id="login-btn">
                    Entrar
                </button>
            </form>
        </div>
    </div>
    
    <!-- Expiration Modal -->
    <div id="expiration-modal" class="modal <?php echo $expired ? 'show' : ''; ?>">
        <div class="modal-content">
            <div class="modal-icon">⚠️</div>
            <h3>Conta Expirada</h3>
            <p>Sua conta expirou. Entre em contato com o administrador para renovar seu acesso.</p>
            <button onclick="document.getElementById('expiration-modal').classList.remove('show')" class="btn">
                Fechar
            </button>
        </div>
    </div>
    
    <script>
        const form = document.getElementById('login-form');
        const alertContainer = document.getElementById('alert-container');
        const loginBtn = document.getElementById('login-btn');
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            loginBtn.disabled = true;
            loginBtn.innerHTML = '<span class="loader"></span>Entrando...';
            alertContainer.innerHTML = '';
            
            try {
                const formData = new FormData();
                formData.append('username', username);
                formData.append('password', password);
                
                const response = await fetch('/login', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = data.redirect;
                } else if (data.expired) {
                    alertContainer.innerHTML = `
                        <div class="alert alert-warning">
                            Sua conta expirou em ${new Date(data.expiration_date).toLocaleDateString('pt-BR')}. 
                            Entre em contato para renovar.
                        </div>
                    `;
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = 'Entrar';
                } else {
                    alertContainer.innerHTML = `
                        <div class="alert alert-error">
                            ${data.error || 'Erro ao fazer login'}
                        </div>
                    `;
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = 'Entrar';
                }
            } catch (error) {
                alertContainer.innerHTML = `
                    <div class="alert alert-error">
                        Erro de conexão. Tente novamente.
                    </div>
                `;
                loginBtn.disabled = false;
                loginBtn.innerHTML = 'Entrar';
            }
        });
    </script>
</body>
</html>
