# Guia de Deployment - Megas Chat

Este guia detalha o processo completo de deployment em um servidor com hospedagem compartilhada usando Nginx.

## 📋 Pré-requisitos

Antes de começar, certifique-se de ter:

- ✅ Acesso SSH ou FTP ao servidor
- ✅ PHP 8.2+ instalado
- ✅ MariaDB/MySQL 5.7+ disponível
- ✅ Nginx configurado
- ✅ Domínio apontado para o servidor (ex: iachat.painelcontrole.xyz)

## 🚀 Passo a Passo

### 1. Preparação Local

Clone ou baixe o repositório:

```bash
git clone https://github.com/Victormegaju/megas.git
cd megas
```

### 2. Upload para o Servidor

#### Via FTP/SFTP (FileZilla, WinSCP, etc.)

1. Conecte-se ao seu servidor FTP
2. Navegue até o diretório raiz do site: `/www/wwwroot/iachat.painelcontrole.xyz`
3. Faça upload de todos os arquivos do repositório
4. Mantenha a estrutura de diretórios intacta

#### Via SSH/SCP

```bash
scp -r * usuario@servidor:/www/wwwroot/iachat.painelcontrole.xyz/
```

#### Via Git (Recomendado)

```bash
# No servidor
cd /www/wwwroot/iachat.painelcontrole.xyz
git clone https://github.com/Victormegaju/megas.git .
```

### 3. Configuração de Permissões

Execute no servidor via SSH:

```bash
cd /www/wwwroot/iachat.painelcontrole.xyz

# Definir permissões corretas
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;

# Permissões especiais para diretório de uploads
chmod 755 uploads/logo
chown www-data:www-data uploads/logo

# Se usar PHP-FPM com usuário específico
chown -R www-data:www-data .
```

### 4. Configuração do Nginx

Edite o arquivo de configuração do Nginx (normalmente em `/etc/nginx/sites-available/iachat.painelcontrole.xyz`):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name iachat.painelcontrole.xyz;
    
    root /www/wwwroot/iachat.painelcontrole.xyz;
    index index.php index.html;

    # Logs
    access_log /var/log/nginx/iachat_access.log;
    error_log /var/log/nginx/iachat_error.log;

    # Aumentar tamanho máximo de upload
    client_max_body_size 10M;

    # Rewrite para index.php
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Processar arquivos PHP
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Timeout para requisições longas (Gemini API)
        fastcgi_read_timeout 300;
    }

    # Negar acesso a arquivos ocultos
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Negar acesso a arquivos sensíveis
    location ~ (config\.php|\.git|\.gitignore) {
        deny all;
        access_log off;
        log_not_found off;
    }
}
```

Ative o site e reinicie o Nginx:

```bash
# Criar symlink se necessário
sudo ln -s /etc/nginx/sites-available/iachat.painelcontrole.xyz /etc/nginx/sites-enabled/

# Testar configuração
sudo nginx -t

# Reiniciar Nginx
sudo systemctl restart nginx
```

### 5. Configuração do PHP

Edite o arquivo `php.ini` (geralmente em `/etc/php/8.2/fpm/php.ini`):

```ini
; Aumentar limites para upload
upload_max_filesize = 10M
post_max_size = 10M
memory_limit = 256M

; Timeout para requisições longas
max_execution_time = 300

; Habilitar extensões necessárias
extension=pdo_mysql
extension=curl
extension=gd
extension=json

; Configuração de timezone
date.timezone = America/Sao_Paulo
```

Reinicie o PHP-FPM:

```bash
sudo systemctl restart php8.2-fpm
```

### 6. Instalação via Web

1. Acesse no navegador: `http://iachat.painelcontrole.xyz/install`

2. Preencha os dados do banco de dados:
   - **Host**: localhost (ou IP do servidor MySQL)
   - **Nome do Banco**: megas_db
   - **Usuário**: seu_usuario_mysql
   - **Senha**: sua_senha_mysql

3. Configure o administrador inicial:
   - **Usuário**: admin (ou outro nome de sua preferência)
   - **Senha**: escolha uma senha forte

4. Clique em "Instalar"

5. Aguarde a conclusão da instalação

### 7. Segurança Pós-Instalação

**CRÍTICO**: Delete o diretório de instalação:

```bash
rm -rf /www/wwwroot/iachat.painelcontrole.xyz/install
```

### 8. Configuração SSL (HTTPS) - Recomendado

Instale certificado SSL usando Let's Encrypt:

```bash
# Instalar certbot
sudo apt install certbot python3-certbot-nginx

# Obter certificado
sudo certbot --nginx -d iachat.painelcontrole.xyz

# Renovação automática (já configurada por padrão)
sudo certbot renew --dry-run
```

Após instalar o SSL, atualize a configuração do Nginx para redirecionar HTTP para HTTPS:

```nginx
server {
    listen 80;
    server_name iachat.painelcontrole.xyz;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name iachat.painelcontrole.xyz;
    
    ssl_certificate /etc/letsencrypt/live/iachat.painelcontrole.xyz/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/iachat.painelcontrole.xyz/privkey.pem;
    
    # Resto da configuração...
}
```

### 9. Configuração de APIs Externas

#### Google Gemini AI

1. Faça login no sistema como admin
2. Vá para **Configurações**
3. Obtenha uma API key em: https://makersuite.google.com/app/apikey
4. Cole a API key no campo "API Key do Gemini"
5. Escolha o modelo (recomendado: `gemini-1.5-flash` para melhor custo-benefício)
6. Salve as configurações

#### Mercado Pago

1. Acesse: https://www.mercadopago.com.br/developers
2. Vá para **Suas integrações** > **Credenciais**
3. Copie as credenciais de **Produção**:
   - Access Token
   - Public Key
4. Cole nas configurações do sistema
5. Configure o Webhook:
   - URL: `https://iachat.painelcontrole.xyz/appeal/webhooks/mercadopago.php`
   - Evento: `payment`
6. Marque "Habilitar Pagamentos"

### 10. Testes

Teste os seguintes aspectos:

1. ✅ Login com usuário admin
2. ✅ Criar um usuário de teste
3. ✅ Fazer login como usuário
4. ✅ Enviar mensagem no chat
5. ✅ Upload de imagem no chat
6. ✅ Upload de logo do site
7. ✅ Criar um revendedor
8. ✅ Fazer login como revendedor
9. ✅ Criar usuário de teste (6h, 12h, 24h)
10. ✅ Testar renovação de usuário

## 🔧 Troubleshooting

### Erro 500 - Internal Server Error

1. Verifique os logs:
```bash
tail -f /var/log/nginx/iachat_error.log
tail -f /var/log/php8.2-fpm.log
```

2. Verifique permissões:
```bash
ls -la /www/wwwroot/iachat.painelcontrole.xyz
```

3. Verifique se o PHP-FPM está rodando:
```bash
sudo systemctl status php8.2-fpm
```

### Erro ao conectar ao banco de dados

1. Verifique se o MySQL está rodando:
```bash
sudo systemctl status mysql
```

2. Teste a conexão:
```bash
mysql -u seu_usuario -p -h localhost megas_db
```

3. Verifique o arquivo `config.php`

### Upload de arquivos não funciona

1. Verifique permissões:
```bash
chmod 755 uploads/logo
chown www-data:www-data uploads/logo
```

2. Verifique limites no php.ini:
```bash
php -i | grep upload_max_filesize
php -i | grep post_max_size
```

### Webhook não recebe notificações

1. Teste se o endpoint está acessível:
```bash
curl -X POST https://iachat.painelcontrole.xyz/appeal/webhooks/mercadopago.php \
  -H "Content-Type: application/json" \
  -d '{"type":"payment","data":{"id":"123"}}'
```

2. Verifique os logs do webhook:
```bash
tail -f /var/log/nginx/iachat_access.log | grep webhook
```

3. Verifique se o firewall permite conexões do Mercado Pago

## 📊 Monitoramento

### Logs Importantes

- **Nginx Access**: `/var/log/nginx/iachat_access.log`
- **Nginx Error**: `/var/log/nginx/iachat_error.log`
- **PHP-FPM**: `/var/log/php8.2-fpm.log`
- **MySQL**: `/var/log/mysql/error.log`

### Comandos Úteis

```bash
# Ver usuários online
mysql -u root -p megas_db -e "SELECT COUNT(*) FROM users WHERE is_active=1"

# Ver pagamentos recentes
mysql -u root -p megas_db -e "SELECT * FROM payments ORDER BY created_at DESC LIMIT 10"

# Ver uso de espaço
du -sh /www/wwwroot/iachat.painelcontrole.xyz

# Verificar processos PHP
ps aux | grep php-fpm
```

## 🔄 Atualizações

Para atualizar o sistema:

```bash
cd /www/wwwroot/iachat.painelcontrole.xyz
git pull origin main

# Fazer backup do banco antes
mysqldump -u root -p megas_db > backup_$(date +%Y%m%d_%H%M%S).sql

# Aplicar migrações se houver
# (instruções específicas serão fornecidas em cada release)
```

## 🔐 Backup

Recomendamos fazer backup regular:

```bash
#!/bin/bash
# Script de backup diário
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/megas"

# Criar diretório de backup
mkdir -p $BACKUP_DIR

# Backup do banco de dados
mysqldump -u root -p megas_db | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Backup dos arquivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /www/wwwroot/iachat.painelcontrole.xyz

# Manter apenas últimos 7 dias
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete

echo "Backup concluído: $DATE"
```

Adicione ao cron para execução diária:

```bash
crontab -e
# Adicionar linha:
0 2 * * * /root/backup_megas.sh
```

## ✅ Checklist Final

Antes de colocar em produção:

- [ ] SSL/HTTPS configurado
- [ ] Diretório /install removido
- [ ] Backup configurado
- [ ] API do Gemini testada
- [ ] Mercado Pago testado (modo sandbox primeiro)
- [ ] Webhook do Mercado Pago configurado
- [ ] Permissões de arquivos corretas
- [ ] Logs sendo salvos corretamente
- [ ] Firewall configurado
- [ ] Senha do admin alterada
- [ ] Monitoramento configurado

## 📞 Suporte

Para questões técnicas ou problemas, consulte:
- Documentação do Nginx: https://nginx.org/en/docs/
- Documentação do PHP: https://www.php.net/docs.php
- Gemini API: https://ai.google.dev/docs
- Mercado Pago: https://www.mercadopago.com.br/developers/pt/docs
