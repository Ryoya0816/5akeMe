<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', (string) config('security.referrer_policy', 'strict-origin-when-cross-origin'));
        $response->headers->set('Permissions-Policy', (string) config('security.permissions_policy', 'camera=(), microphone=(), geolocation=()'));

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $response->headers->set('Content-Security-Policy', $this->buildCsp());

        return $response;
    }

    private function buildCsp(): string
    {
        /** @var array<string, array<int, string>> $directives */
        $directives = config('security.csp', []);

        if (! app()->isProduction()) {
            foreach (config('security.csp_non_production', []) as $directive => $tokens) {
                if (! isset($directives[$directive])) {
                    $directives[$directive] = [];
                }
                $directives[$directive] = array_values(array_merge($directives[$directive], $tokens));
            }
        }

        $parts = [];
        foreach ($directives as $name => $tokens) {
            if ($tokens === []) {
                continue;
            }
            $parts[] = $name.' '.implode(' ', $tokens);
        }

        return implode('; ', $parts);
    }
}
