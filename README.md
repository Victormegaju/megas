# Megas Chat - Sistema de Chat AI com Gestão de Usuários

Sistema completo de chat com inteligência artificial (Gemini AI) incluindo gestão de usuários, revendedores e pagamentos via PIX (Mercado Pago).

## 🚀 Características

- **Autenticação**: Sistema de login com username e senha (sem email)
- **Três tipos de usuário**: Admin, Revendedor (Revenda), e Usuário
- **Gestão Completa**: Criar, editar, suspender, renovar e deletar usuários
- **Chat AI**: Integração com Google Gemini AI (texto e imagens)
- **Pagamentos PIX**: Integração com Mercado Pago para renovações automáticas
- **Usuários de Teste**: Revendedores podem criar usuários com 6h, 12h ou 24h de acesso
- **Sistema de Expiração**: Bloqueio automático após vencimento
- **Interface Moderna**: Design responsivo com gradientes coloridos

## 📋 Requisitos

- PHP 8.2 ou superior
- MariaDB/MySQL 5.7 ou superior
- Nginx ou Apache
- Extensões PHP:
  - PDO
  - PDO_MySQL
  - cURL
  - JSON
  - GD (para upload de imagens)

## 📦 Instalação

### 1. Upload dos Arquivos

Faça upload de todos os arquivos para o diretório público do seu servidor (ex: `/www/wwwroot/iachat.painelcontrole.xyz`).

### 2. Configuração do Nginx

Adicione a seguinte configuração ao seu site no Nginx:

```nginx
server {
    listen 80;
    server_name iachat.painelcontrole.xyz;
    root /www/wwwroot/iachat.painelcontrole.xyz;
    index index.php;

    # Rewrite all requests to index.php
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM configuration
    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }
}
```

Reinicie o Nginx:
```bash
sudo systemctl restart nginx
```

### 3. Permissões de Diretório

Certifique-se de que o diretório `uploads/logo` tenha permissões de escrita:

```bash
chmod 755 uploads/logo
```

### 4. Instalação via Web

1. Acesse: `http://iachat.painelcontrole.xyz/install`
2. Preencha as credenciais do banco de dados
3. Defina o usuário e senha do administrador inicial
4. Clique em "Instalar"

### 5. Segurança Pós-Instalação

**IMPORTANTE**: Delete o diretório `/install` após a instalação:

```bash
rm -rf /www/wwwroot/iachat.painelcontrole.xyz/install
```

## ⚙️ Configuração

### Gemini AI

1. Faça login como administrador
2. Vá para **Configurações**
3. Configure:
   - **API Key do Gemini**: Sua chave da API do Google AI Studio
   - **Modelo do Gemini**: Escolha entre `gemini-pro`, `gemini-1.5-pro`, ou `gemini-1.5-flash`

Para obter uma chave API do Gemini:
1. Acesse [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Crie uma nova API key
3. Cole a chave nas configurações

### Mercado Pago (Pagamentos PIX)

1. Faça login como administrador
2. Vá para **Configurações**
3. Configure:
   - **Access Token**: Token de acesso da sua conta Mercado Pago
   - **Public Key**: Chave pública da sua conta
   - **Webhook Signature Key**: Chave de assinatura do webhook
   - **Habilitar Pagamentos**: Marque para ativar

4. Configure o Webhook no painel do Mercado Pago:
   - URL: `https://iachat.painelcontrole.xyz/appeal/webhooks/mercadopago.php`
   - Eventos: `payment`

Para obter as credenciais do Mercado Pago:
1. Acesse o [Dashboard do Mercado Pago](https://www.mercadopago.com.br/developers)
2. Vá para **Suas integrações** > **Credenciais**
3. Use as credenciais de **Produção**

### Upload de Logo

1. Faça login como administrador
2. Vá para **Configurações**
3. Na seção "Logo do Site", faça upload de uma imagem (JPG, PNG, GIF, WEBP - máx 2MB)
4. O logo aparecerá na tela de login e em outras páginas

## 👥 Tipos de Usuário

### Administrador
- Dashboard com estatísticas gerais
- Gerenciar todos os usuários e revendedores
- Configurar API do Gemini e Mercado Pago
- Upload de logo do site
- Suspender, ativar, renovar e deletar contas

### Revendedor (Revenda)
- Dashboard com estatísticas dos próprios usuários
- Criar e gerenciar usuários regulares
- Criar usuários de teste (6h, 12h, 24h)
- Ver dias restantes da própria conta
- Renovar acesso via pagamento PIX (se habilitado)

### Usuário
- Interface de chat com Gemini AI
- Enviar mensagens de texto e imagens
- Histórico de conversas
- Ver dias restantes da conta
- Renovar acesso via pagamento PIX (se habilitado)
- Alterar senha

## 🔐 Segurança

- Senhas são armazenadas com hash usando `password_hash()` do PHP
- Validação de sessão em todas as páginas protegidas
- Verificação de expiração em cada requisição
- API Key do Gemini nunca exposta ao cliente
- Webhook protegido por verificação de assinatura
- Idempotência nos webhooks de pagamento

## 📱 Funcionalidades Principais

### Chat AI
- Interface moderna de chat
- Suporte a texto e imagens
- Histórico de conversas salvo no banco de dados
- Indicador de digitação
- Upload de imagens com preview
- Limpeza de histórico

### Gestão de Usuários
- CRUD completo (Criar, Ler, Atualizar, Deletar)
- Definir dias de acesso
- Suspender/ativar contas
- Renovar acesso (adicionar dias)
- Usuários de teste com duração limitada

### Pagamentos
- Geração de QR Code PIX
- Código PIX para copiar e colar
- Verificação automática de status
- Atualização automática de dias após aprovação
- Registro completo de pagamentos

## 🗄️ Estrutura do Banco de Dados

### Tabela `users`
- Armazena todos os usuários (admin, revenda, usuario)
- Controle de expiração e status
- Hierarquia (created_by)

### Tabela `settings`
- Configurações globais do sistema
- API keys e credenciais
- Preferências do site

### Tabela `payments`
- Registro de todos os pagamentos
- Integração com Mercado Pago
- Status e histórico

### Tabela `chat_history`
- Histórico de conversas
- Mensagens de usuário e assistente
- Indicação de imagens

## 🔧 Troubleshooting

### Erro ao fazer login
- Verifique se o banco de dados está acessível
- Confirme que o usuário existe na tabela `users`
- Verifique os logs do PHP

### Chat não funciona
- Verifique se a API Key do Gemini está configurada
- Teste a API Key diretamente no Google AI Studio
- Verifique os logs de erro do PHP

### Pagamentos não funcionam
- Verifique as credenciais do Mercado Pago
- Confirme que o webhook está configurado corretamente
- Verifique se o endpoint do webhook está acessível publicamente
- Consulte os logs em `/var/log/nginx/error.log` ou `/var/log/php-fpm/error.log`

### Imagens não fazem upload
- Verifique permissões do diretório `uploads/logo`
- Confirme o tamanho máximo de upload no php.ini
- Verifique `upload_max_filesize` e `post_max_size`

## 📝 Licença

Este projeto é proprietário. Todos os direitos reservados.

## 👨‍💻 Suporte

Para suporte técnico, entre em contato com o administrador do sistema.