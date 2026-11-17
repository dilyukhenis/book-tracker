<?php
/** @var array<string, string> $errors */
/** @var array<string, string> $old */
?>
<div class="card" style="max-width: 420px; margin: 0 auto;">
    <h1>Вход</h1>
    <form method="post" action="/login" novalidate>
        <input type="hidden" name="_csrf" value="<?= e(\App\Csrf::token()) ?>">

        <div class="field">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" value="<?= e($old['email'] ?? '') ?>" required autofocus>
            <?php if (isset($errors['email'])): ?>
                <div class="error"><?= e($errors['email']) ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>
            <?php if (isset($errors['password'])): ?>
                <div class="error"><?= e($errors['password']) ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="primary">Войти</button>
    </form>
    <p class="small muted" style="margin-bottom: 0;">Нет аккаунта? <a href="/register">Зарегистрироваться</a></p>
</div>
