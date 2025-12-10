<?php
// auth.php
require_once 'config.php';

class Auth {
    private $db;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $database = new Database();
        $this->db = $database->connect();
    }
    
    public function login($email, $senha) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? AND senha = MD5(?)");
        $stmt->execute([$email, $senha]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_tipo'] = $user['tipo'];
            $_SESSION['is_admin'] = $user['is_admin'];
            return true;
        }
        return false;
    }
    
    public function logout() {
        session_destroy();
        header('Location: login.php');
        exit();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public function isAdmin() {
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
    }
    
    public function requireLogin() {
        if(!$this->isLoggedIn()) {
            header('Location: login.php');
            exit();
        }
    }
    
    public function requireAdmin() {
        if(!$this->isAdmin()) {
            die('Acesso negado. Apenas administradores.');
        }
    }
    
    public function getUserName() {
        return $_SESSION['user_nome'] ?? '';
    }
    
    public function getUserEmail() {
        return $_SESSION['user_email'] ?? '';
    }
    
    public function getUserId() {
        return $_SESSION['user_id'] ?? null;
    }
}
?>