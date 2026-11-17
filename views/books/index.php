<?php
/** @var array{items: array<int, array>, total: int, page: int, perPage: int, pages: int} $result */
/** @var string $search */
/** @var array<string, string> $statuses */

$link = static function (int $page) use ($search): string {
    $query = ['page' => $page];
    if ($search !== '') {
        $query['q'] = $search;
    }

    return '/books?' . http_build_query($query);
};
?>
<div class="toolbar">
    <h1 style="margin: 0;">Мои книги <span class="muted small">(<?= $result['total'] ?>)</span></h1>
    <a class="button primary" href="/books/create">Добавить книгу</a>
</div>

<div class="card">
    <form method="get" action="/books" class="search">
        <input type="search" name="q" value="<?= e($search) ?>" placeholder="Поиск по названию или автору">
        <button type="submit">Найти</button>
        <?php if ($search !== ''): ?>
            <a class="button" href="/books">Сбросить</a>
        <?php endif; ?>
    </form>
</div>

<?php if ($result['items'] === []): ?>
    <div class="card muted">
        <?= $search !== '' ? 'Ничего не найдено по запросу «' . e($search) . '».' : 'Пока нет ни одной книги.' ?>
    </div>
<?php else: ?>
    <div class="card" style="padding: 0 20px;">
        <table>
            <thead>
            <tr>
                <th>Название</th>
                <th>Автор</th>
                <th>Год</th>
                <th>Статус</th>
                <th>Оценка</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($result['items'] as $book): ?>
                <tr>
                    <td><?= e($book['title']) ?></td>
                    <td><?= e($book['author']) ?></td>
                    <td><?= $book['year'] !== null ? e($book['year']) : '—' ?></td>
                    <td><?= e($statuses[$book['status']] ?? $book['status']) ?></td>
                    <td><?= $book['rating'] !== null ? str_repeat('★', (int)$book['rating']) : '—' ?></td>
                    <td style="white-space: nowrap;">
                        <a href="/books/<?= (int)$book['id'] ?>/edit">Изменить</a>
                        <form method="post" action="/books/<?= (int)$book['id'] ?>/delete" class="inline"
                              onsubmit="return confirm('Удалить книгу?');">
                            <input type="hidden" name="_csrf" value="<?= e(\App\Csrf::token()) ?>">
                            <button type="submit" class="link-danger">Удалить</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($result['pages'] > 1): ?>
        <nav class="pagination">
            <?php if ($result['page'] > 1): ?>
                <a href="<?= e($link($result['page'] - 1)) ?>">← Назад</a>
            <?php endif; ?>

            <?php for ($page = 1; $page <= $result['pages']; $page++): ?>
                <?php if ($page === $result['page']): ?>
                    <span class="current"><?= $page ?></span>
                <?php else: ?>
                    <a href="<?= e($link($page)) ?>"><?= $page ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($result['page'] < $result['pages']): ?>
                <a href="<?= e($link($result['page'] + 1)) ?>">Вперёд →</a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
