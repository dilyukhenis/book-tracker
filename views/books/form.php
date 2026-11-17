<?php
/** @var array<string, mixed> $book */
/** @var array<string, string> $errors */
/** @var string $action */
/** @var array<string, string> $statuses */
?>
<div class="card" style="max-width: 560px;">
    <h1><?= e($title) ?></h1>

    <form method="post" action="<?= e($action) ?>" novalidate>
        <input type="hidden" name="_csrf" value="<?= e(\App\Csrf::token()) ?>">

        <div class="field">
            <label for="title">Название</label>
            <input type="text" id="title" name="title" value="<?= e($book['title'] ?? '') ?>" required maxlength="200" autofocus>
            <?php if (isset($errors['title'])): ?>
                <div class="error"><?= e($errors['title']) ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="author">Автор</label>
            <input type="text" id="author" name="author" value="<?= e($book['author'] ?? '') ?>" required maxlength="120">
            <?php if (isset($errors['author'])): ?>
                <div class="error"><?= e($errors['author']) ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="year">Год издания (необязательно)</label>
            <input type="number" id="year" name="year" value="<?= e($book['year'] ?? '') ?>" min="1000" max="<?= (int)date('Y') ?>">
            <?php if (isset($errors['year'])): ?>
                <div class="error"><?= e($errors['year']) ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="status">Статус</label>
            <select id="status" name="status">
                <?php foreach ($statuses as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= ($book['status'] ?? 'planned') === $value ? 'selected' : '' ?>>
                        <?= e($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['status'])): ?>
                <div class="error"><?= e($errors['status']) ?></div>
            <?php endif; ?>
        </div>

        <div class="field">
            <label for="rating">Оценка (только для прочитанных)</label>
            <select id="rating" name="rating">
                <option value="">—</option>
                <?php for ($rating = 1; $rating <= 5; $rating++): ?>
                    <option value="<?= $rating ?>" <?= (int)($book['rating'] ?? 0) === $rating ? 'selected' : '' ?>>
                        <?= str_repeat('★', $rating) ?>
                    </option>
                <?php endfor; ?>
            </select>
            <?php if (isset($errors['rating'])): ?>
                <div class="error"><?= e($errors['rating']) ?></div>
            <?php endif; ?>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <button type="submit" class="primary">Сохранить</button>
            <a href="/books">Отмена</a>
        </div>
    </form>
</div>
