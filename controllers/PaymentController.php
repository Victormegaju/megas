<?php
// Payment Controller
class PaymentController {
    private $db;
    private $user;
    
    public function __construct($db, $user) {
        $this->db = $db;
        $this->user = $user;
    }
    
    public function handle($uri, $method) {
        if ($uri === '/payment/create' && $method === 'POST') {
            $this->createPayment();
        } elseif (preg_match('#^/payment/checkout#', $uri)) {
            $this->showCheckout();
        } elseif (preg_match('#^/payment/status#', $uri)) {
            $this->getStatus();
        } else {
            http_response_code(404);
            echo '404 - Not Found';
        }
    }
    
    private function getStatus() {
        header('Content-Type: application/json');
        
        $paymentId = $_GET['id'] ?? null;
        
        if (!$paymentId) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid payment ID']);
            return;
        }
        
        $payment = $this->db->fetchOne('SELECT status FROM payments WHERE id = ? AND user_id = ?', [$paymentId, $this->user->id]);
        
        if (!$payment) {
            http_response_code(404);
            echo json_encode(['error' => 'Payment not found']);
            return;
        }
        
        echo json_encode(['status' => $payment['status']]);
    }
    
    private function createPayment() {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        $type = $data['type'] ?? 'renewal';
        $days = $data['days'] ?? 30;
        $amount = $data['amount'] ?? 0;
        
        if ($amount <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid amount']);
            return;
        }
        
        // Get Mercado Pago settings
        $settings = new Settings($this->db);
        $accessToken = $settings->getMercadoPagoAccessToken();
        
        if (empty($accessToken)) {
            http_response_code(500);
            echo json_encode(['error' => 'Payment system not configured']);
            return;
        }
        
        try {
            // Create payment in Mercado Pago
            $mpPayment = $this->createMercadoPagoPayment($accessToken, $amount, "Renovação - {$days} dias");
            
            // Save payment to database
            $sql = 'INSERT INTO payments (user_id, external_id, amount, status, payment_type, days_added, payment_data) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)';
            
            $this->db->execute($sql, [
                $this->user->id,
                $mpPayment['id'],
                $amount,
                'pending',
                $type,
                $days,
                json_encode($mpPayment)
            ]);
            
            $paymentId = $this->db->lastInsertId();
            
            echo json_encode([
                'success' => true,
                'payment_id' => $paymentId,
                'mp_payment_id' => $mpPayment['id']
            ]);
            
        } catch (Exception $e) {
            error_log('Payment creation error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create payment: ' . $e->getMessage()]);
        }
    }
    
    private function createMercadoPagoPayment($accessToken, $amount, $description) {
        $url = 'https://api.mercadopago.com/v1/payments';
        
        $payload = [
            'transaction_amount' => floatval($amount),
            'description' => $description,
            'payment_method_id' => 'pix',
            'payer' => [
                'email' => $this->user->username . '@placeholder.com',
            ]
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 201) {
            $error = json_decode($response, true);
            throw new Exception($error['message'] ?? 'Payment creation failed');
        }
        
        return json_decode($response, true);
    }
    
    private function showCheckout() {
        $paymentId = $_GET['id'] ?? null;
        
        if (!$paymentId) {
            http_response_code(400);
            echo 'Invalid payment ID';
            return;
        }
        
        // Get payment from database
        $payment = $this->db->fetchOne('SELECT * FROM payments WHERE id = ? AND user_id = ?', [$paymentId, $this->user->id]);
        
        if (!$payment) {
            http_response_code(404);
            echo 'Payment not found';
            return;
        }
        
        $paymentData = json_decode($payment['payment_data'], true);
        
        // Extract QR code and payment info
        $qrCodeBase64 = $paymentData['point_of_interaction']['transaction_data']['qr_code_base64'] ?? null;
        $qrCode = $paymentData['point_of_interaction']['transaction_data']['qr_code'] ?? null;
        
        require_once __DIR__ . '/../views/payment/checkout.php';
    }
}
