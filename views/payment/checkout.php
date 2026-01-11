<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Megas Chat</title>
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
        .checkout-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        .amount {
            font-size: 36px;
            font-weight: 700;
            color: #667eea;
            text-align: center;
            margin-bottom: 30px;
        }
        .qr-section {
            text-align: center;
            margin-bottom: 30px;
        }
        .qr-code {
            max-width: 300px;
            border: 4px solid #667eea;
            border-radius: 12px;
            padding: 10px;
            background: white;
            margin: 0 auto 20px;
        }
        .pix-code {
            background: #f8fafc;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            word-break: break-all;
            font-family: monospace;
            font-size: 12px;
            margin-bottom: 15px;
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
            margin-bottom: 15px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #f8fafc;
            color: #333;
            border: 2px solid #e5e7eb;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }
        .status {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-weight: 600;
        }
        .status.pending {
            background: #fef3c7;
            color: #92400e;
        }
        .status.approved {
            background: #d1fae5;
            color: #065f46;
        }
    </style>
</head>
<body>
    <div class="checkout-card">
        <h1>💳 Pagamento PIX</h1>
        <div class="amount">R$ <?php echo number_format($payment['amount'], 2, ',', '.'); ?></div>
        
        <div class="alert">
            ℹ️ Escaneie o QR Code ou copie o código PIX para completar o pagamento.
            Após o pagamento, seus dias serão adicionados automaticamente.
        </div>
        
        <div class="qr-section">
            <?php if ($qrCodeBase64): ?>
                <img src="data:image/png;base64,<?php echo $qrCodeBase64; ?>" alt="QR Code PIX" class="qr-code">
            <?php endif; ?>
            
            <?php if ($qrCode): ?>
                <div class="pix-code" id="pix-code"><?php echo htmlspecialchars($qrCode); ?></div>
                <button class="btn" onclick="copyPixCode()">📋 Copiar Código PIX</button>
            <?php endif; ?>
        </div>
        
        <div id="status" class="status pending">
            ⏳ Aguardando pagamento...
        </div>
        
        <button class="btn btn-secondary" onclick="checkPaymentStatus()" style="margin-top: 15px;">
            🔄 Verificar Status
        </button>
        
        <a href="/profile" class="btn btn-secondary" style="display: block; text-align: center; text-decoration: none; margin-top: 10px;">
            ← Voltar
        </a>
    </div>
    
    <script>
        const paymentId = <?php echo $payment['id']; ?>;
        
        function copyPixCode() {
            const pixCode = document.getElementById('pix-code').textContent;
            navigator.clipboard.writeText(pixCode).then(() => {
                alert('Código PIX copiado!');
            });
        }
        
        async function checkPaymentStatus() {
            try {
                const response = await fetch('/payment/status?id=' + paymentId);
                const data = await response.json();
                
                const statusDiv = document.getElementById('status');
                
                if (data.status === 'approved') {
                    statusDiv.className = 'status approved';
                    statusDiv.innerHTML = '✅ Pagamento aprovado! Redirecionando...';
                    setTimeout(() => {
                        window.location.href = '/profile';
                    }, 2000);
                } else if (data.status === 'rejected') {
                    statusDiv.className = 'status pending';
                    statusDiv.innerHTML = '❌ Pagamento rejeitado';
                } else {
                    statusDiv.className = 'status pending';
                    statusDiv.innerHTML = '⏳ Aguardando pagamento...';
                }
            } catch (error) {
                alert('Erro ao verificar status');
            }
        }
        
        // Auto-check status every 5 seconds
        setInterval(checkPaymentStatus, 5000);
    </script>
</body>
</html>
