<?php

declare(strict_types=1);

namespace App\Framework\Response;

class RawResponse extends Response
{
    protected array $headers = [
        'Content-Type: text/html; charset=utf-8'
    ];

    protected function encode($data): void
    {
        $this->encoded = $data;
    }
}
