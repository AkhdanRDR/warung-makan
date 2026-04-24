<?php

use App\Helpers\BaseResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api(prepend: [
            \Illuminate\Session\Middleware\StartSession::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        ]);

        // Register middleware aliases
        $middleware->alias([
            'check.token.idle' => \App\Http\Middleware\CheckTokenIdle::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if (!$request->is('api/*')) {
                return null;
            }
            if (str_contains($e->getMessage(), 'Route [login] not defined')) {
                return BaseResponse::Error('Akses ditolak. Silakan masukkan token Anda.', null, Response::HTTP_UNAUTHORIZED);
            }


            // Custom API error responses
            switch (true) {
                case $e instanceof ModelNotFoundException:
                    return BaseResponse::Error('Model tidak ditemukan ' . $e->getMessage(), null, Response::HTTP_NOT_FOUND);
                case $e instanceof NotFoundHttpException:
                    return BaseResponse::Error('Sumber tidak ditemukan ' . $e->getMessage(), null, Response::HTTP_NOT_FOUND);

                case $e instanceof ValidationException:
                    return BaseResponse::Error('Validasi gagal ' . $e->getMessage(), $e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);

                case $e instanceof AuthenticationException:
                    return BaseResponse::Error('Akses tidak sah ' . $e->getMessage(), null, Response::HTTP_UNAUTHORIZED);

                case $e instanceof AuthorizationException:
                    return BaseResponse::Error('Dilarang: Anda tidak memiliki izin ' . $e->getMessage(), null, Response::HTTP_FORBIDDEN);

                case $e instanceof TooManyRequestsHttpException:
                case $e instanceof ThrottleRequestsException:
                    return BaseResponse::Error('Terlalu banyak permintaan, harap perlambat ' . $e->getMessage(), null, Response::HTTP_TOO_MANY_REQUESTS);

                case $e instanceof QueryException:
                    return BaseResponse::Error('Terjadi kesalahan database ' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);

                case $e instanceof HttpException:
                    $status = $e->getStatusCode();
                    $message = match ($status) {
                        Response::HTTP_NOT_FOUND => 'Sumber tidak ditemukan ' . $e->getMessage(),
                        Response::HTTP_UNAUTHORIZED => 'Akses tidak sah ' . $e->getMessage(),
                        Response::HTTP_FORBIDDEN => 'Dilarang: Anda tidak memiliki izin ' . $e->getMessage(),
                        Response::HTTP_INTERNAL_SERVER_ERROR => 'Terjadi kesalahan server ' . $e->getMessage(),
                        Response::HTTP_BAD_REQUEST => 'Permintaan tidak valid ' . $e->getMessage(),
                        Response::HTTP_UNPROCESSABLE_ENTITY => 'Entitas tidak dapat diproses ' . $e->getMessage(),
                        Response::HTTP_METHOD_NOT_ALLOWED => 'Metode tidak diizinkan ' . $e->getMessage(),
                        Response::HTTP_NOT_ACCEPTABLE => 'Tidak dapat diterima ' . $e->getMessage(),
                        Response::HTTP_CONFLICT => 'Konflik ' . $e->getMessage(),
                        Response::HTTP_PRECONDITION_FAILED => 'Prasyarat Gagal ' . $e->getMessage(),
                        Response::HTTP_UNSUPPORTED_MEDIA_TYPE => 'Tipe Media Tidak Didukung ' . $e->getMessage(),
                        default => 'Kesalahan Tidak Dikenal ' . $e->getMessage(),
                    };

                    return BaseResponse::Error($message, null, $status);

                case $e instanceof RuntimeException:
                    return BaseResponse::Error('Terjadi kesalahan runtime ' . $e->getMessage(), null, Response::HTTP_INTERNAL_SERVER_ERROR);

                default:
                    $status = $e instanceof HttpException
                        ? $e->getStatusCode()
                        : Response::HTTP_INTERNAL_SERVER_ERROR;

                    return BaseResponse::Error(
                        $e->getMessage() ?: 'Terjadi kesalahan yang tidak diketahui',
                        null,
                        $status
                    );
            }
        });
    })->create();
