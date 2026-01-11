<?php
// Settings model
class Settings {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function get($key, $default = null) {
        $data = $this->db->fetchOne('SELECT setting_value FROM settings WHERE setting_key = ?', [$key]);
        return $data ? $data['setting_value'] : $default;
    }
    
    public function set($key, $value) {
        $sql = 'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?';
        return $this->db->execute($sql, [$key, $value, $value]);
    }
    
    public function getAll() {
        $rows = $this->db->fetchAll('SELECT setting_key, setting_value FROM settings');
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }
    
    public function getGeminiApiKey() {
        return $this->get('gemini_api_key');
    }
    
    public function getGeminiModel() {
        return $this->get('gemini_model', 'gemini-pro');
    }
    
    public function getMercadoPagoAccessToken() {
        return $this->get('mp_access_token');
    }
    
    public function getMercadoPagoPublicKey() {
        return $this->get('mp_public_key');
    }
    
    public function getMercadoPagoWebhookKey() {
        return $this->get('mp_webhook_signature_key');
    }
    
    public function isPaymentsEnabled() {
        return $this->get('mp_payments_enabled', '0') === '1';
    }
    
    public function getSiteLogo() {
        return $this->get('site_logo');
    }
}
