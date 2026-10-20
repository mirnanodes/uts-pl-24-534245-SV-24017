<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class HealthController extends Controller
{
    /**
     * GET /api/health — used by Docker healthchecks and the CI smoke test.
     *
     * "server" shows which PHP SAPI handled the request:
     * "frankenphp" in Docker, "cli-server" with `php artisan serve`.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $databaseUp = $this->databaseIsReachable();

        return response()->json([
            'status' => $databaseUp ? 'ok' : 'degraded',
            'app' => config('app.name'),
            'server' => PHP_SAPI,
            'server_software' => $request->server('SERVER_SOFTWARE'),
            'php_version' => PHP_VERSION,
            'database' => $databaseUp ? 'up' : 'down',
            'time' => now()->toIso8601String(),
        ], $databaseUp ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE);
    }

    private function databaseIsReachable(): bool
    {
        try {
            DB::connection()->getPdo();

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
