<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private function tokensPath(): string
    {
        return storage_path('app/tokens.json');
    }

    private function saveToken(string $token): void
    {
        $path = $this->tokensPath();
        $tokens = file_exists($path)
            ? json_decode(file_get_contents($path), true) ?? []
            : [];
        $tokens[] = $token;
        file_put_contents($path, json_encode($tokens));
    }

    public static function isValidToken(string $token): bool
    {
        $path = storage_path('app/tokens.json');
        if (!file_exists($path)) {
            return false;
        }
        $tokens = json_decode(file_get_contents($path), true) ?? [];
        return in_array($token, $tokens, true);
    }

    public function token(Request $request): JsonResponse
    {
        $username = $request->json('username');
        $password = $request->json('password');

        if ($username !== 'admin' || $password !== 'password') {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $token = bin2hex(random_bytes(32));
        $this->saveToken($token);

        return response()->json(['token' => $token], 200);
    }
}
