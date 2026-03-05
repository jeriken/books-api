<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    private function booksPath(): string
    {
        return storage_path('app/books.json');
    }

    private function readBooks(): array
    {
        $path = $this->booksPath();
        if (!file_exists($path)) {
            return [];
        }
        return json_decode(file_get_contents($path), true) ?? [];
    }

    private function writeBooks(array $books): void
    {
        file_put_contents($this->booksPath(), json_encode(array_values($books), JSON_PRETTY_PRINT));
    }

    public function index(): JsonResponse
    {
        return response()->json($this->readBooks(), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $books = $this->readBooks();
        $book = [
            'id'     => count($books) > 0 ? max(array_column($books, 'id')) + 1 : 1,
            'title'  => $request->json('title'),
            'author' => $request->json('author'),
            'year'   => $request->json('year'),
        ];
        $books[] = $book;
        $this->writeBooks($books);
        return response()->json($book, 201);
    }

    public function show(int $id): JsonResponse
    {
        foreach ($this->readBooks() as $book) {
            if ((int) $book['id'] === $id) {
                return response()->json($book, 200);
            }
        }
        return response()->json(['error' => 'Book not found'], 404);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $books = $this->readBooks();
        foreach ($books as &$book) {
            if ((int) $book['id'] === $id) {
                $book['title']  = $request->json('title', $book['title']);
                $book['author'] = $request->json('author', $book['author']);
                $book['year']   = $request->json('year', $book['year']);
                $this->writeBooks($books);
                return response()->json($book, 200);
            }
        }
        return response()->json(['error' => 'Book not found'], 404);
    }

    public function destroy(int $id): JsonResponse
    {
        $books = $this->readBooks();
        foreach ($books as $index => $book) {
            if ((int) $book['id'] === $id) {
                unset($books[$index]);
                $this->writeBooks($books);
                return response()->json(['success' => true], 200);
            }
        }
        return response()->json(['error' => 'Book not found'], 404);
    }

}
