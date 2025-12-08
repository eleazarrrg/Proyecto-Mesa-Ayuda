<?php
// app/helpers/Response.php

class Response
{
    public static function redirect(string $url)
    {
        header("Location: {$url}");
        exit;
    }

    public static function setFlash(string $key, string $message, string $type = 'info')
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        $_SESSION['flash'][$key] = [
            'message' => $message,
            'type'    => $type
        ];
    }

    public static function getFlash(string $key): ?array
    {
        if (!isset($_SESSION)) {
            session_start();
        }

        if (!isset($_SESSION['flash'][$key])) {
            return null;
        }

        $data = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $data;
    }
}
