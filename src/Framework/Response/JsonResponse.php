<?php

declare(strict_types=1);

namespace App\Framework\Response;

class JsonResponse extends Response
{
    protected array $headers = [
        'Content-Type: application/json; charset=utf-8'
    ];

    protected function encode($data): void
    {
        $result = json_encode($data);

        if ($result == false) {
            $this->status = 500;
            $result = '{"message": "error encoding"}';
        }

        $this->encoded = $result;
    }
}
