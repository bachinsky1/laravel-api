<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // Если запрос API (определяем по префиксу /api)
        if ($request->expectsJson() || $request->is('api/*')) {

            // Обработка 404 ошибки (не найден маршрут или ресурс)
            if ($exception instanceof NotFoundHttpException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Route not found'
                ], 404);
            }

            // Обработка случая, если не найдена модель (например, Post::findOrFail($id))
            if ($exception instanceof ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Resource not found'
                ], 404);
            }

            // Ошибки валидации (например, неверный формат данных)
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $exception->errors()
                ], 422);
            }

            // Ошибка аутентификации
            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated'
                ], 401);
            }

            // Обработка HTTP-ошибок (403, 500 и т. д.)
            if ($exception instanceof HttpException) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage()
                ], $exception->getStatusCode());
            }

            // Обработка всех остальных исключений
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $exception->getMessage()
            ], 500);
        }

        // Для обычных HTML-ответов используем стандартное поведение
        return parent::render($request, $exception);
    }
}
