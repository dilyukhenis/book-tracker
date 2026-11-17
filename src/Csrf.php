<?php
declare(strict_types=1);

namespace App;

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf'];
    }

    public static function check(): void
    {
        $sent = $_POST['_csrf'] ?? '';

        if (!is_string($sent) || !hash_equals($_SESSION['_csrf'] ?? '', $sent)) {
            http_response_code(419);
            exit('CSRF-токен недействителен. Обновите страницу.');
        }
    }
}
