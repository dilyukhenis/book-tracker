<?php
declare(strict_types=1);

namespace App;

final class View
{
    public static function render(string $template, array $data = []): void
    {
        $file = BASE_PATH . '/views/' . $template . '.php';
        if (!is_file($file)) {
            http_response_code(500);
            echo 'Шаблон не найден: ' . e($template);
            return;
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $file;
        $content = (string)ob_get_clean();

        require BASE_PATH . '/views/layout.php';
    }
}
