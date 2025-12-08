<?php
// app/core/Validator.php
class Validator {

    public static function sanitizeString($str) {
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    public static function isEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function required($value) {
        return isset($value) && trim($value) !== '';
    }

    public static function maxLength($value, $max) {
        return mb_strlen($value) <= $max;
    }

    // Puedes ir añadiendo métodos según lo necesites (fechas, enteros, etc.)
}
