<?php
// app/core/Auth.php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Validator.php';

class Auth {

    public static function login($username, $password) {
        $db = Database::getInstance()->getConnection();

        $sql = "SELECT * FROM usuarios WHERE username = :username AND activo = 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['role_id'] = $user['rol_id'];
            return true;
        }
        return false;
    }

    public static function check() {
        return isset($_SESSION['user_id']);
    }

    public static function logout() {
        session_destroy();
    }
}
