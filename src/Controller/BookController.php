<?php
declare(strict_types=1);

namespace App\Controller;

use App\Auth;
use App\BookRepository;
use App\Csrf;
use App\Database;
use App\Flash;
use App\Validator;
use App\View;

final class BookController
{
    private BookRepository $books;

    public function __construct()
    {
        $this->books = new BookRepository(Database::pdo());
    }

    public function index(array $params = []): void
    {
        Auth::requireLogin();

        $search = trim((string)($_GET['q'] ?? ''));
        $page = max(1, (int)($_GET['page'] ?? 1));

        View::render('books/index', [
            'title'    => 'Мои книги',
            'result'   => $this->books->paginate(Auth::id(), $search, $page),
            'search'   => $search,
            'statuses' => BookRepository::STATUSES,
        ]);
    }

    public function create(array $params = []): void
    {
        Auth::requireLogin();

        View::render('books/form', [
            'title'    => 'Новая книга',
            'action'   => '/books',
            'book'     => $this->emptyBook(),
            'errors'   => [],
            'statuses' => BookRepository::STATUSES,
        ]);
    }

    public function store(array $params = []): void
    {
        Auth::requireLogin();
        Csrf::check();

        [$data, $errors] = $this->validate($_POST);

        if ($errors !== []) {
            http_response_code(422);
            $this->renderForm('Новая книга', '/books', $data, $errors);

            return;
        }

        $this->books->create(Auth::id(), $data);
        Flash::set('success', 'Книга добавлена.');
        redirect('/books');
    }

    public function edit(array $params = []): void
    {
        Auth::requireLogin();

        $book = $this->books->find((int)($params['id'] ?? 0), Auth::id());
        if ($book === null) {
            $this->notFound();

            return;
        }

        $this->renderForm('Редактирование книги', '/books/' . $book['id'], $book, []);
    }

    public function update(array $params = []): void
    {
        Auth::requireLogin();
        Csrf::check();

        $id = (int)($params['id'] ?? 0);
        if ($this->books->find($id, Auth::id()) === null) {
            $this->notFound();

            return;
        }

        [$data, $errors] = $this->validate($_POST);

        if ($errors !== []) {
            http_response_code(422);
            $this->renderForm('Редактирование книги', '/books/' . $id, $data + ['id' => $id], $errors);

            return;
        }

        $this->books->update($id, Auth::id(), $data);
        Flash::set('success', 'Изменения сохранены.');
        redirect('/books');
    }

    public function destroy(array $params = []): void
    {
        Auth::requireLogin();
        Csrf::check();

        $deleted = $this->books->delete((int)($params['id'] ?? 0), Auth::id());

        if (!$deleted) {
            $this->notFound();

            return;
        }

        Flash::set('success', 'Книга удалена.');
        redirect('/books');
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, string>}
     */
    private function validate(array $input): array
    {
        $validator = (new Validator($input))
            ->required(['title' => 'Название', 'author' => 'Автор'])
            ->length('title', 1, 200, 'Название')
            ->length('author', 1, 120, 'Автор')
            ->integer('year', 1000, (int)date('Y'), 'Год')
            ->integer('rating', 1, 5, 'Оценка')
            ->in('status', array_keys(BookRepository::STATUSES), 'Статус');

        $errors = $validator->errors();

        $year = trim((string)($input['year'] ?? ''));
        $rating = trim((string)($input['rating'] ?? ''));

        $data = [
            'title'  => trim((string)($input['title'] ?? '')),
            'author' => trim((string)($input['author'] ?? '')),
            'year'   => $year === '' ? null : (int)$year,
            'status' => (string)($input['status'] ?? 'planned'),
            'rating' => $rating === '' ? null : (int)$rating,
        ];
                // бизнес-правило: оценка имеет смысл только для прочитанной книги
        if ($data['status'] !== 'finished') {
            $data['rating'] = null;
        }

        return [$data, $errors];
    }

    private function emptyBook(): array
    {
        return [
            'id'     => null,
            'title'  => '',
            'author' => '',
            'year'   => null,
            'status' => 'planned',
            'rating' => null,
        ];
    }

    private function renderForm(string $title, string $action, array $book, array $errors): void
    {
        View::render('books/form', [
            'title'    => $title,
            'action'   => $action,
            'book'     => $book,
            'errors'   => $errors,
            'statuses' => BookRepository::STATUSES,
        ]);
    }

    private function notFound(): void
    {
        http_response_code(404);
        View::render('errors/404', ['title' => 'Книга не найдена']);
    }
}
