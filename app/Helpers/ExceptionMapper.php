<?php

namespace App\Helpers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

class ExceptionMapper
{
    public static function getStatusCode(\Throwable $exception): int
    {
        return match (true) {
            $exception instanceof ModelNotFoundException => 404,
            $exception instanceof AuthenticationException => 401,
            $exception instanceof ValidationException => 422,
            default => 400,
        };
    }
}
