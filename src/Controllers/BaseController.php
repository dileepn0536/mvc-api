<?php

namespace Dileep\Mvc\Controllers;

abstract class BaseController
{
    protected function getJsonData(): ?array
    {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        return $data;
    }

    protected function jsonResponse(bool $status, string $dataOrMessage, int $code = 200)    {
        header('Content-Type: application/json');
        http_response_code($code);

        $response = ['status' => $status];
        
        if ($status) {
            $response['data'] = $dataOrMessage;
        } else {
            $response['message'] = $dataOrMessage;
        }

        return $response;
    }
}