<?php
// User model
class User {
    public $id;
    public $username;
    public $role;
    public $created_by;
    public $created_at;
    public $updated_at;
    public $expiration_date;
    public $is_active;
    public $is_test_user;
    
    private $db;
    
    public function __construct($db, $data = []) {
        $this->db = $db;
        if (!empty($data)) {
            $this->fill($data);
        }
    }
    
    private function fill($data) {
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? null;
        $this->role = $data['role'] ?? 'usuario';
        $this->created_by = $data['created_by'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        $this->expiration_date = $data['expiration_date'] ?? null;
        $this->is_active = $data['is_active'] ?? 1;
        $this->is_test_user = $data['is_test_user'] ?? 0;
    }
    
    public static function findById($db, $id) {
        $data = $db->fetchOne('SELECT * FROM users WHERE id = ?', [$id]);
        return $data ? new self($db, $data) : null;
    }
    
    public static function findByUsername($db, $username) {
        $data = $db->fetchOne('SELECT * FROM users WHERE username = ?', [$username]);
        return $data ? new self($db, $data) : null;
    }
    
    public static function authenticate($db, $username, $password) {
        $user = self::findByUsername($db, $username);
        if ($user && password_verify($password, $user->getPasswordHash())) {
            return $user;
        }
        return null;
    }
    
    private function getPasswordHash() {
        $data = $this->db->fetchOne('SELECT password_hash FROM users WHERE id = ?', [$this->id]);
        return $data['password_hash'] ?? null;
    }
    
    public function isActive() {
        if (!$this->is_active) {
            return false;
        }
        
        // Check expiration
        $now = new DateTime();
        $expiration = new DateTime($this->expiration_date);
        
        return $now <= $expiration;
    }
    
    public function isAdmin() {
        return $this->role === 'admin';
    }
    
    public function isRevenda() {
        return $this->role === 'revenda';
    }
    
    public function isUsuario() {
        return $this->role === 'usuario';
    }
    
    public function getRemainingDays() {
        $now = new DateTime();
        $expiration = new DateTime($this->expiration_date);
        $interval = $now->diff($expiration);
        
        if ($now > $expiration) {
            return 0;
        }
        
        return $interval->days;
    }
    
    public function save() {
        if ($this->id) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }
    
    private function insert() {
        if (!isset($this->password_hash)) {
            throw new Exception('Password hash must be set before creating user');
        }
        
        $sql = 'INSERT INTO users (username, password_hash, role, created_by, expiration_date, is_active, is_test_user) 
                VALUES (?, ?, ?, ?, ?, ?, ?)';
        
        $this->db->execute($sql, [
            $this->username,
            $this->password_hash,
            $this->role,
            $this->created_by,
            $this->expiration_date,
            $this->is_active,
            $this->is_test_user
        ]);
        
        $this->id = $this->db->lastInsertId();
        return $this->id;
    }
    
    private function update() {
        $sql = 'UPDATE users SET 
                username = ?, 
                role = ?, 
                expiration_date = ?, 
                is_active = ?,
                is_test_user = ?
                WHERE id = ?';
        
        return $this->db->execute($sql, [
            $this->username,
            $this->role,
            $this->expiration_date,
            $this->is_active,
            $this->is_test_user,
            $this->id
        ]);
    }
    
    public function delete() {
        if (!$this->id) {
            return false;
        }
        
        $sql = 'DELETE FROM users WHERE id = ?';
        return $this->db->execute($sql, [$this->id]);
    }
    
    public function updatePassword($newPassword) {
        $sql = 'UPDATE users SET password_hash = ? WHERE id = ?';
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->db->execute($sql, [$hash, $this->id]);
    }
    
    public function addDays($days) {
        $expiration = new DateTime($this->expiration_date);
        $expiration->modify("+{$days} days");
        $this->expiration_date = $expiration->format('Y-m-d H:i:s');
        return $this->update();
    }
    
    public function getCreatedUsers() {
        if (!$this->isRevenda()) {
            return [];
        }
        
        $sql = 'SELECT * FROM users WHERE created_by = ? ORDER BY created_at DESC';
        $users = $this->db->fetchAll($sql, [$this->id]);
        
        return array_map(function($data) {
            return new User($this->db, $data);
        }, $users);
    }
    
    public static function getAll($db, $role = null) {
        if ($role) {
            $sql = 'SELECT * FROM users WHERE role = ? ORDER BY created_at DESC';
            $users = $db->fetchAll($sql, [$role]);
        } else {
            $sql = 'SELECT * FROM users ORDER BY created_at DESC';
            $users = $db->fetchAll($sql);
        }
        
        return array_map(function($data) use ($db) {
            return new User($db, $data);
        }, $users);
    }
}
