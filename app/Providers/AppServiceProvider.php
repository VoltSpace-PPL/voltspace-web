<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::getAccessTokenFromRequestUsing(function (Request $request): ?string {
            foreach (['Authorization', 'X-Authorization'] as $headerName) {
                $raw = $request->header($headerName);
                if (! is_string($raw) || $raw === '') {
                    continue;
                }

                if (preg_match('/Bearer\s+(.+)/i', $raw, $matches)) {
                    $token = trim($matches[1]);
                    $token = trim($token, " \t\n\r\0\x0B\"'");
                    $token = preg_replace('/^Bearer\s+/i', '', $token) ?? $token;
                    $token = trim((string) $token, " \t\n\r\0\x0B\"'");
                    if ($token !== '') {
                        return $token;
                    }
                }
            }

            return $request->bearerToken();
        });
    }
}
