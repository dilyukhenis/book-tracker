<?php
declare(strict_types=1);

session_start();

define('BASE_PATH', dirname(__DIR__));

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $file = BASE_PATH . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

require BASE_PATH . '/src/helpers.php';

use App\Controller\AuthController;
use App\Controller\BookController;
use App\Router;

$auth = new AuthController();
$books = new BookController();
$router = new Router();

$router->get('/', [$books, 'index']);
$router->get('/books', [$books, 'index']);
$router->get('/books/create', [$books, 'create']);
$router->post('/books', [$books, 'store']);
$router->get('/books/{id}/edit', [$books, 'edit']);
$router->post('/books/{id}', [$books, 'update']);
$router->post('/books/{id}/delete', [$books, 'destroy']);

$router->get('/login', [$auth, 'showLogin']);
$router->post('/login', [$auth, 'login']);
$router->get('/register', [$auth, 'showRegister']);
$router->post('/register', [$auth, 'register']);
$router->post('/logout', [$auth, 'logout']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
