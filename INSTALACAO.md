# Megas Chat - Quick Start Guide

## 📦 O que está neste ZIP

Este arquivo contém uma aplicação web completa de chat com IA (Gemini AI), sistema de pagamentos PIX (Mercado Pago) e gerenciamento de usuários.

## 🚀 Instalação Rápida (5 passos)

### 1. Upload dos Arquivos
Extraia e faça upload de todos os arquivos para o diretório público do seu servidor:
```
/www/wwwroot/iachat.painelcontrole.xyz
```

### 2. Configure o Nginx
Use o arquivo `nginx.conf.example` como referência para configurar seu servidor Nginx.

Configuração mínima necessária:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 3. Defina Permissões
```bash
chmod 755 uploads/logo
```

### 4. Execute o Instalador
Acesse no navegador:
```
http://seu-dominio.com/install
```

Preencha:
- Credenciais do banco de dados MySQL/MariaDB
- Usuário e senha do administrador inicial

### 5. Segurança
**IMPORTANTE**: Delete o diretório `/install` após a instalação:
```bash
rm -rf install/
```

## ⚙️ Configuração Pós-Instalação

1. **Faça login como administrador**
   - Vá para: `/login`
   - Use as credenciais criadas na instalação

2. **Configure APIs (em /admin/settings)**
   - **Gemini AI**: 
     - Obtenha key em: https://makersuite.google.com/app/apikey
     - Cole no campo "API Key do Gemini"
   
   - **Mercado Pago** (opcional):
     - Credenciais em: https://www.mercadopago.com.br/developers
     - Configure webhook: `https://seu-dominio.com/appeal/webhooks/mercadopago.php`

3. **Upload de Logo** (opcional)
   - Vá para Configurações
   - Faça upload de uma imagem (JPG, PNG, GIF, WEBP - máx 2MB)

## 👥 Tipos de Usuário

### Administrador
- Gerenciar todos os usuários e revendedores
- Configurar APIs e sistema
- Suspender, ativar, renovar contas

### Revendedor (Revenda)
- Criar e gerenciar próprios usuários
- Criar usuários de teste (6h, 12h, 24h)
- Ver expiração da própria conta

### Usuário
- Usar chat com IA
- Enviar textos e imagens
- Renovar própria conta (se pagamentos habilitados)

## 📋 Requisitos do Servidor

- **PHP**: 8.2 ou superior
- **Banco de Dados**: MariaDB 5.7+ ou MySQL 5.7+
- **Servidor Web**: Nginx (com PHP-FPM)
- **Extensões PHP**: PDO, PDO_MySQL, cURL, JSON, GD

## 📚 Documentação Completa

Consulte os arquivos incluídos:
- `README.md` - Documentação completa para usuários
- `DEPLOYMENT.md` - Guia detalhado de deployment
- `PROJECT_SUMMARY.md` - Resumo técnico do projeto

## 🔧 Verificação da Instalação

Execute o script de verificação:
```bash
./verify-installation.sh
```

Deve mostrar: **40 passed, 0 failed**

## 🆘 Problemas Comuns

### Erro ao fazer login
- Verifique se o banco de dados está acessível
- Confirme que criou o usuário admin na instalação

### Chat não funciona
- Configure a API Key do Gemini nas configurações
- Verifique se a key está válida no Google AI Studio

### Upload de logo não funciona
- Verifique permissões: `chmod 755 uploads/logo`
- Confirme limites de upload no php.ini

## 📞 Estrutura de Arquivos

```
/
├── index.php              # Front controller (ponto de entrada)
├── schema.sql             # Schema do banco de dados
├── config.template.php    # Template de configuração
├── classes/               # Classes do sistema
├── controllers/           # Controllers MVC
├── views/                 # Templates das páginas
├── install/               # Instalador web (deletar após uso!)
├── appeal/webhooks/       # Webhooks de pagamento
└── uploads/logo/          # Logos enviados
```

## ✅ Próximos Passos

1. ✅ Extrair arquivos no servidor
2. ✅ Configurar Nginx
3. ✅ Executar /install
4. ✅ Deletar /install
5. ✅ Configurar APIs
6. ✅ Criar primeiro usuário/revendedor
7. ✅ Testar chat com IA

## 🎉 Pronto!

Sua aplicação está pronta para uso. Acesse `/login` para começar!

---

**Versão**: 1.0.0  
**Data**: Dezembro 2024  
**Suporte**: Consulte README.md para documentação completa
