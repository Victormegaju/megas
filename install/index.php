<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador - Megas Chat</title>
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
        .installer {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        p {
            color: #666;
            margin-bottom: 30px;
        }
        .step {
            margin-bottom: 30px;
        }
        .step h2 {
            color: #667eea;
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .step-number {
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
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
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 30px;
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
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-error {
            background: #fee;
            color: #c00;
            border: 1px solid #fcc;
        }
        .alert-success {
            background: #efe;
            color: #0a0;
            border: 1px solid #cfc;
        }
        .alert-info {
            background: #eef;
            color: #00a;
            border: 1px solid #ccf;
        }
        .success-icon {
            font-size: 60px;
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="installer">
        <?php
        $step = $_GET['step'] ?? '1';
        $config_exists = file_exists(__DIR__ . '/../config.php');
        
        if ($step === '1' && !$config_exists): ?>
            <h1>🚀 Instalador Megas Chat</h1>
            <p>Configure o banco de dados e crie o administrador inicial.</p>
            
            <form method="POST" action="/install?step=2">
                <div class="step">
                    <h2><span class="step-number">1</span>Configuração do Banco de Dados</h2>
                    
                    <div class="form-group">
                        <label for="db_host">Host do Banco de Dados</label>
                        <input type="text" id="db_host" name="db_host" value="localhost" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="db_name">Nome do Banco de Dados</label>
                        <input type="text" id="db_name" name="db_name" value="megas_db" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="db_user">Usuário do Banco de Dados</label>
                        <input type="text" id="db_user" name="db_user" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="db_pass">Senha do Banco de Dados</label>
                        <input type="password" id="db_pass" name="db_pass">
                    </div>
                </div>
                
                <div class="step">
                    <h2><span class="step-number">2</span>Administrador Inicial</h2>
                    
                    <div class="form-group">
                        <label for="admin_user">Nome de Usuário</label>
                        <input type="text" id="admin_user" name="admin_user" value="admin" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="admin_pass">Senha</label>
                        <input type="password" id="admin_pass" name="admin_pass" required>
                    </div>
                </div>
                
                <button type="submit" class="btn">Instalar</button>
            </form>
            
        <?php elseif ($step === '2' && $_SERVER['REQUEST_METHOD'] === 'POST'): 
            // Process installation
            $db_host = $_POST['db_host'] ?? 'localhost';
            $db_name = $_POST['db_name'] ?? 'megas_db';
            $db_user = $_POST['db_user'] ?? '';
            $db_pass = $_POST['db_pass'] ?? '';
            $admin_user = $_POST['admin_user'] ?? 'admin';
            $admin_pass = $_POST['admin_pass'] ?? '';
            
            $errors = [];
            
            try {
                // Test database connection
                $dsn = "mysql:host={$db_host};charset=utf8mb4";
                $pdo = new PDO($dsn, $db_user, $db_pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Create database if not exists
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo->exec("USE `{$db_name}`");
                
                // Run schema
                $schema = file_get_contents(__DIR__ . '/../schema.sql');
                // Remove USE statement from schema as we already selected the database
                $schema = preg_replace('/USE\s+\w+;/', '', $schema);
                $pdo->exec($schema);
                
                // Create admin user
                $expiration = (new DateTime())->modify('+365 days')->format('Y-m-d H:i:s');
                $password_hash = password_hash($admin_pass, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, role, expiration_date, is_active) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$admin_user, $password_hash, 'admin', $expiration, 1]);
                
                // Create config file
                $config_content = "<?php\nreturn [\n";
                $config_content .= "    'db' => [\n";
                $config_content .= "        'host' => '{$db_host}',\n";
                $config_content .= "        'database' => '{$db_name}',\n";
                $config_content .= "        'username' => '{$db_user}',\n";
                $config_content .= "        'password' => '{$db_pass}',\n";
                $config_content .= "        'charset' => 'utf8mb4'\n";
                $config_content .= "    ],\n";
                $config_content .= "    'app' => [\n";
                $config_content .= "        'name' => 'Megas Chat',\n";
                $config_content .= "        'timezone' => 'America/Sao_Paulo',\n";
                $config_content .= "        'session_lifetime' => 7200,\n";
                $config_content .= "    ],\n";
                $config_content .= "    'upload' => [\n";
                $config_content .= "        'logo_path' => __DIR__ . '/uploads/logo/',\n";
                $config_content .= "        'max_size' => 2097152\n";
                $config_content .= "    ]\n";
                $config_content .= "];\n";
                
                file_put_contents(__DIR__ . '/../config.php', $config_content);
                
                ?>
                <div class="success-icon">✅</div>
                <h1>Instalação Concluída!</h1>
                <p>O sistema foi instalado com sucesso.</p>
                
                <div class="alert alert-success">
                    <strong>Administrador criado:</strong><br>
                    Usuário: <strong><?php echo htmlspecialchars($admin_user); ?></strong><br>
                    Senha: <strong>***</strong> (a senha que você definiu)
                </div>
                
                <div class="alert alert-info">
                    <strong>Próximos passos:</strong><br>
                    1. Delete o diretório <code>/install</code> por segurança<br>
                    2. Configure o Nginx conforme o README<br>
                    3. Faça login e configure a API do Gemini e Mercado Pago
                </div>
                
                <a href="/login" class="btn" style="display: block; text-align: center; text-decoration: none; margin-top: 20px;">
                    Ir para Login
                </a>
                
            <?php } catch (Exception $e) {
                ?>
                <h1>Erro na Instalação</h1>
                <div class="alert alert-error">
                    <strong>Erro:</strong> <?php echo htmlspecialchars($e->getMessage()); ?>
                </div>
                <a href="/install" class="btn" style="display: block; text-align: center; text-decoration: none; margin-top: 20px;">
                    Tentar Novamente
                </a>
                <?php
            }
            
        else: ?>
            <h1>Instalação já concluída</h1>
            <div class="alert alert-info">
                O sistema já está instalado. Se deseja reinstalar, delete o arquivo <code>config.php</code>.
            </div>
            <a href="/login" class="btn" style="display: block; text-align: center; text-decoration: none; margin-top: 20px;">
                Ir para Login
            </a>
        <?php endif; ?>
    </div>
</body>
</html>
