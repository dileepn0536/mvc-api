<?php

namespace Dileep\Mvc\Core;

use Dileep\Mvc\Exceptions\NotFoundException;
use Dileep\Mvc\Exceptions\ValidationException;
use Dileep\Mvc\Exceptions\UnauthorizedException;

class ExceptionHandler
{
    public static function handle(\Throwable $e): array
    {
        error_log($e->getMessage());

        return match(true) {
            $e instanceof NotFoundException     => self::notFound($e),
            $e instanceof ValidationException   => self::validation($e),
            $e instanceof UnauthorizedException => self::unauthorized($e),
            default                             => self::serverError($e)
        };
    }

    private static function notFound(\Throwable $e): array
    {
        return [
            'status'  => false,
            'message' => $e->getMessage(),
            'code'    => 404
        ];
    }

    private static function validation(\Throwable $e): array
    {
        return [
            'status'  => false,
            'message' => $e->getMessage(),
            'code'    => 422
        ];
    }

    private static function unauthorized(\Throwable $e): array
    {
        return [
            'status'  => false,
            'message' => $e->getMessage(),
            'code'    => 401
        ];
    }

    private static function serverError(\Throwable $e): array
    {
        error_log($e->getMessage());

        $message = (getenv("APP_ENV") == 'dev') ? $e->getMessage() : "Internal server error";
        return [
            'status'  => false,
            'message' => $message,
            'code'    => 500
        ];
    }
}