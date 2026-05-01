<?php

declare(strict_types=1);

namespace App\Support;

use App\Constants\Code;

trait ApiResponse
{
    public function success(
        mixed  $data = null,
        string $message = 'success',
        int    $code = Code::SUCCESS
    ): array
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
    }

    public function error(
        string $message = 'error',
        int    $code = Code::SERVER_ERROR,
        mixed  $data = null
    ): array
    {
        return [
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ];
    }
}