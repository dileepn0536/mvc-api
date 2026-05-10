<?php

namespace Dileep\Mvc\Core;

use Dileep\Mvc\Exceptions\NotFoundException;
use Dileep\Mvc\Exceptions\ValidationException;
use Dileep\Mvc\Exceptions\UnauthorizedException;

class ExceptionHandler
{
    public function handle(\Throwable $e): array
    {
        error_log($e->getMessage());

        return match(true) {
            $e instanceof NotFoundException     => $this->notFound($e),
            $e instanceof ValidationException   => $this->validation($e),
            $e instanceof UnauthorizedException => $this->unauthorized($e),
            default                             => $this->serverError($e)
        };
    }

    private function notFound(\Throwable $e): array
    {
        return [
            'status'  => false,
            'message' => $e->getMessage(),
            'code'    => 404
        ];
    }

    private function validation(\Throwable $e): array
    {
        return [
            'status'  => false,
            'message' => $e->getMessage(),
            'code'    => 422
        ];
    }

    private function unauthorized(\Throwable $e): array
    {
        return [
            'status'  => false,
            'message' => $e->getMessage(),
            'code'    => 401
        ];
    }

    private function serverError(\Throwable $e): array
    {
        return [
            'status'  => false,
            'message' => 'Internal server error',
            'code'    => 500
        ];
    }
}