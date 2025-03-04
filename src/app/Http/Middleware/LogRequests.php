<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequests
{
    public function handle(Request $request, Closure $next)
    {
        // Логируем входящий запрос
        Log::info('Incoming Request', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'body' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        $response = $next($request);

        // Логируем ответ
        Log::info('Response', [
            'status' => $response->status(),
            'content' => $response->getContent()
        ]);

        return $response;
    }

}
