<?php
/** @var string $content */
$currentUser = \App\Auth::user();
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Дневник книг') ?></title>
    <style>
        :root { --border: #d6d9de; --muted: #6b7280; --accent: #2563eb; }
        * { box-sizing: border-box; }
        body { margin: 0; font: 16px/1.5 -apple-system, "Segoe UI", Roboto, sans-serif; color: #111827; background: #f8f9fb; }
        header { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 24px; background: #fff; border-bottom: 1px solid var(--border); }
        header .brand { font-weight: 600; text-decoration: none; color: inherit; }
        nav { display: flex; align-items: center; gap: 12px; font-size: 14px; }
        main { max-width: 900px; margin: 24px auto; padding: 0 24px 48px; }
        a { color: var(--accent); }
        h1 { font-size: 22px; margin: 0 0 16px; }
        .card { background: #fff; border: 1px solid var(--border); border-radius: 10px; padding: 20px; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        th, td { text-align: left; padding: 10px 8px; border-bottom: 1px solid var(--border); vertical-align: top; }
        th { color: var(--muted); font-weight: 500; font-size: 13px; }
        .field { margin-bottom: 14px; }
        label { display: block; font-size: 13px; color: var(--muted); margin-bottom: 4px; }
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="search"], select {
            width: 100%; padding: 8px 10px; border: 1px solid var(--border); border-radius: 6px; font: inherit; background: #fff;
        }
        .error { color: #b91c1c; font-size: 13px; margin-top: 4px; }
        .flash { padding: 10px 14px; border-radius: 8px; margin-bottom: 12px; font-size: 14px; }
        .flash-success { background: #ecfdf5; color: #065f46; }
        .flash-error { background: #fef2f2; color: #991b1b; }
        .muted { color: var(--muted); }
        .small { font-size: 13px; }
        form.inline { display: inline; }
        button, .button { padding: 8px 14px; border-radius: 6px; border: 1px solid var(--border); background: #fff; font: inherit; cursor: pointer; text-decoration: none; color: inherit; }
        .primary { background: var(--accent); border-color: var(--accent); color: #fff; }
        .link-danger { color: #b91c1c; background: none; border: 0; padding: 0; cursor: pointer; font: inherit; text-decoration: underline; }
        .toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
        .search { display: flex; gap: 8px; flex: 1; min-width: 240px; }
        .pagination { display: flex; gap: 6px; margin-top: 16px; font-size: 14px; flex-wrap: wrap; }
        .pagination .current { padding: 4px 10px; background: var(--accent); color: #fff; border-radius: 6px; }
        .pagination a { padding: 4px 10px; border: 1px solid var(--border); border-radius: 6px; text-decoration: none; }
    </style>
</head>
<body>
<header>
    <a class="brand" href="/books">Дневник книг</a>
    <nav>
        <?php if ($currentUser !== null): ?>
            <span class="muted small"><?= e($currentUser['email']) ?></span>
            <form method="post" action="/logout" class="inline">
                <input type="hidden" name="_csrf" value="<?= e(\App\Csrf::token()) ?>">
                <button type="submit">Выйти</button>
            </form>
        <?php else: ?>
            <a href="/login">Вход</a>
            <a href="/register">Регистрация</a>
        <?php endif; ?>
    </nav>
</header>
<main>
    <?php foreach (\App\Flash::take() as $flash): ?>
        <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endforeach; ?>
    <?= $content ?>
</main>
</body>
</html>
