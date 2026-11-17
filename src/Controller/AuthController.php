<?php
declare(strict_types=1);

namespace App\Controller;

use App\Auth;
use App\Csrf;
use App\Database;
use App\Flash;
use App\Validator;
use App\View;

final class AuthController
{
    public function showLogin(array $params = []): void
    {
        if (Auth::id() !== null) {
            redirect('/books');
        }

        View::render('auth/login', [
            'title'  => 'Вход',
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function login(array $params = []): void
    {
        Csrf::check();

        $email = trim((string)($_POST['email'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        $validator = (new Validator($_POST))
            ->required(['email' => 'E-mail', 'password' => 'Пароль']);

        $errors = $validator->errors();

        if ($errors === [] && !Auth::login($email, $password)) {
            $errors['password'] = 'Неверный e-mail или пароль.';
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('auth/login', [
                'title'  => 'Вход',
                'errors' => $errors,
                'old'    => ['email' => $email],
            ]);

            return;
        }

        Flash::set('success', 'Вы вошли в аккаунт.');
        redirect('/books');
    }

    public function showRegister(array $params = []): void
    {
        if (Auth::id() !== null) {
            redirect('/books');
        }

        View::render('auth/register', [
            'title'  => 'Регистрация',
            'errors' => [],
            'old'    => [],
        ]);
    }

    public function register(array $params = []): void
    {
        Csrf::check();

        $email = mb_strtolower(trim((string)($_POST['email'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        $passwordConfirm = (string)($_POST['password_confirm'] ?? '');

        $validator = (new Validator($_POST))
            ->required(['email' => 'E-mail', 'password' => 'Пароль', 'password_confirm' => 'Повтор пароля'])
            ->length('email', 5, 190, 'E-mail')
            ->length('password', 8, 72, 'Пароль');

        $errors = $validator->errors();

        if (!isset($errors['email']) && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Некорректный e-mail.';
        }

        if (!isset($errors['password_confirm']) && $password !== $passwordConfirm) {
            $errors['password_confirm'] = 'Пароли не совпадают.';
        }

        if (!isset($errors['email'])) {
            $stmt = Database::pdo()->prepare('SELECT 1 FROM users WHERE email = ?');
            $stmt->execute([$email]);

            if ($stmt->fetchColumn() !== false) {
                $errors['email'] = 'Такой e-mail уже зарегистрирован.';
            }
        }

        if ($errors !== []) {
            http_response_code(422);
            View::render('auth/register', [
                'title'  => 'Регистрация',
                'errors' => $errors,
                'old'    => ['email' => $email],
            ]);

            return;
        }

        // bcrypt обрезает пароль после 72 байт — длина ограничена в валидаторе выше
        $stmt = Database::pdo()->prepare(
            'INSERT INTO users (email, password_hash) VALUES (?, ?)'
        );
        $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);

        Auth::login($email, $password);
        Flash::set('success', 'Аккаунт создан. Добавьте первую книгу.');
        redirect('/books');
    }

    public function logout(array $params = []): void
    {
        Csrf::check();
        Auth::logout();
        Flash::set('success', 'Вы вышли из аккаунта.');
        redirect('/login');
    }
}
