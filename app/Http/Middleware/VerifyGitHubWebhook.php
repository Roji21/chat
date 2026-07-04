<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyGitHubWebhook
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ambil signature dari header GitHub
        $gitHubSignature = $request->header('X-Hub-Signature-256');

        if (!$gitHubSignature) {
            return response()->json(['error' => 'Header tidak ditemukan.'], 403);
        }

        // 2. Ambil Secret Key dari file .env
        $secret = env('GITHUB_WEBHOOK_SECRET');

        // 3. Buat hash tandingan menggunakan konten data yang masuk
        $payload = $request->getContent();
        $knownSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

        // 4. Cocokkan hash dari GitHub dengan hash buatan server kita
        if (!hash_equals($knownSignature, $gitHubSignature)) {
            return response()->json(['error' => 'Kunci rahasia tidak cocok! Akses ditolak.'], 403);
        }

        return $next($request);
    }
}
