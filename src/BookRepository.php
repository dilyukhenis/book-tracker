<?php
declare(strict_types=1);

namespace App;

use PDO;

final class BookRepository
{
    public const STATUSES = [
        'planned'  => 'В планах',
        'reading'  => 'Читаю',
        'finished' => 'Прочитана',
    ];

    public function __construct(private PDO $pdo)
    {
    }

    /**
     * @return array{items: array<int, array>, total: int, page: int, perPage: int, pages: int}
     */
    public function paginate(int $userId, string $search, int $page, int $perPage = 10): array
    {
        $perPage = max(1, min($perPage, 50));
        $page = max(1, $page);

        $where = 'user_id = :user_id';
        $params = [':user_id' => $userId];

        if ($search !== '') {
            $where .= " AND (title LIKE :q_title ESCAPE '\\' OR author LIKE :q_author ESCAPE '\\')";
            $like = '%' . $this->escapeLike($search) . '%';
            $params[':q_title'] = $like;
            $params[':q_author'] = $like;
        }

        $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM books WHERE {$where}");
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        $pages = max(1, (int)ceil($total / $perPage));
        $page = min($page, $pages);
        $offset = ($page - 1) * $perPage;

        $stmt = $this->pdo->prepare(
            "SELECT * FROM books WHERE {$where}
             ORDER BY created_at DESC, id DESC
             LIMIT :limit OFFSET :offset"
        );

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'items'   => $stmt->fetchAll(),
            'total'   => $total,
            'page'    => $page,
            'perPage' => $perPage,
            'pages'   => $pages,
        ];
    }

    /**
     * Поиск строго в пределах пользователя — защита от подмены id в URL.
     */
    public function find(int $id, int $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM books WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        $book = $stmt->fetch();

        return $book ?: null;
    }

    public function create(int $userId, array $data): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO books (user_id, title, author, year, status, rating)
             VALUES (:user_id, :title, :author, :year, :status, :rating)'
        );
        $stmt->execute([
            ':user_id' => $userId,
            ':title'   => $data['title'],
            ':author'  => $data['author'],
            ':year'    => $data['year'],
            ':status'  => $data['status'],
            ':rating'  => $data['rating'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function update(int $id, int $userId, array $data): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE books
                SET title = :title, author = :author, year = :year,
                    status = :status, rating = :rating
              WHERE id = :id AND user_id = :user_id'
        );

        return $stmt->execute([
            ':title'   => $data['title'],
            ':author'  => $data['author'],
            ':year'    => $data['year'],
            ':status'  => $data['status'],
            ':rating'  => $data['rating'],
            ':id'      => $id,
            ':user_id' => $userId,
        ]);
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM books WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);

        return $stmt->rowCount() > 0;
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
