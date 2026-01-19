<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SanitizeInput
{
    /**
     * 入力値のサニタイズ
     * XSS攻撃を防ぐために危険な文字をエスケープ
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();

        // 再帰的にサニタイズ
        $sanitized = $this->sanitizeArray($input);

        $request->merge($sanitized);

        return $next($request);
    }

    /**
     * 配列を再帰的にサニタイズ
     */
    private function sanitizeArray(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            // キーもサニタイズ
            $sanitizedKey = $this->sanitizeString((string) $key);

            if (is_array($value)) {
                $result[$sanitizedKey] = $this->sanitizeArray($value);
            } elseif (is_string($value)) {
                $result[$sanitizedKey] = $this->sanitizeString($value);
            } else {
                $result[$sanitizedKey] = $value;
            }
        }

        return $result;
    }

    /**
     * 文字列をサニタイズ
     */
    private function sanitizeString(string $value): string
    {
        // NULLバイトを除去
        $value = str_replace("\0", '', $value);

        // 制御文字を除去（タブ、改行、キャリッジリターンは許可）
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

        // 前後の空白をトリム
        $value = trim($value);

        return $value;
    }
}
