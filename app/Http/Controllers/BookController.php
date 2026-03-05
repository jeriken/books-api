<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    private function getStoragePath(): string
    {
        return storage_path('app/books.json');
    }

    private function getBooks(): array
    {
        $path = $this->getStoragePath();
        if (!file_exists($path)) {
            return [];
        }
        return json_decode(file_get_contents($path), true) ?? [];
    }

    private function saveBooks(array $books): void
    {
        file_put_contents($this->getStoragePath(), json_encode(array_values($books), JSON_PRETTY_PRINT));
    }

    public function index(Request $request): JsonResponse
    {
        $books = $this->getBooks();

        // Filter by author
        if ($request->has('author')) {
            $author = strtolower($request->query('author'));
            $books = array_values(array_filter($books, function ($book) use ($author) {
                return str_contains(strtolower($book['author']), $author);
            }));
        }

        // Pagination
        if ($request->has('page') || $request->has('limit')) {
            $page  = max(1, (int) $request->query('page', 1));
            $limit = max(1, (int) $request->query('limit', 10));
            $books = array_values(array_slice($books, ($page - 1) * $limit, $limit));
        }

        return response()->json($books, 200);
    }

    public function store(Request $request): JsonResponse
    {
        $title  = $request->json('title');
        $author = $request->json('author');
        $year   = $request->json('year');

        if (empty($title) || empty($author) || empty($year)) {
            return response()->json(['error' => 'title, author, and year are required'], 400);
        }

        $books = $this->getBooks();
        $book = [
            'id'     => count($books) > 0 ? max(array_column($books, 'id')) + 1 : 1,
            'title'  => $title,
            'author' => $author,
            'year'   => $year,
        ];
        $books[] = $book;
        $this->saveBooks($books);
        return response()->json($book, 201);
    }

    public function show(string $id): JsonResponse
    {
        foreach ($this->getBooks() as $book) {
            if ((string) $book['id'] === $id) {
                return response()->json($book, 200);
            }
        }
        return response()->json(['error' => 'Book not found'], 404);
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $books = $this->getBooks();
        foreach ($books as &$book) {
            if ((string) $book['id'] === $id) {
                $book['title']  = $request->json('title', $book['title']);
                $book['author'] = $request->json('author', $book['author']);
                $book['year']   = $request->json('year', $book['year']);
                $this->saveBooks($books);
                return response()->json($book, 200);
            }
        }
        return response()->json(['error' => 'Book not found'], 404);
    }

    public function destroy(string $id): JsonResponse
    {
        $books = $this->getBooks();
        foreach ($books as $index => $book) {
            if ((string) $book['id'] === $id) {
                unset($books[$index]);
                $this->saveBooks($books);
                return response()->json(['success' => true], 200);
            }
        }
        return response()->json(['error' => 'Book not found'], 404);
    }

}
