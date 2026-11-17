<?php
/** @var array<string, string> $errors */
/** @var array<string, string> $old */
?>
<div class="card" style="max-width: 420px; margin: 0 auto;">
    <h1>Регистрация</h1>
    <form method="post" action="/register" novalidate>
        <input type="hidden" name="_csrf" value="<?= e(\App\Csrf::token()) ?>">

        <div class="field">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" required autofocus>
            <?php if (isset($errors['email'])): ?>
                <div class="error"><?= e($errors['email']) ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="password">Пароль (минимум 8 символов)</label>
            <input type="password" id="password" name="password" required>
            <?php if (isset($errors['password'])): ?>
                <div class="error"><?= e($errors['password']) ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="password_confirm">Повтор пароля</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
            <?php if (isset($errors['password_confirm'])): ?>
                <div class="error"><?= e($errors['password_confirm']) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="primary">Создать аккаунт</button>
    </form>
    <p class="small muted" style="margin-bottom: 0;">Уже есть аккаунт? <a href="/login">Войти</a></p>
</div>
