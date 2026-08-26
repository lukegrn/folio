<?php

declare(strict_types=1);

namespace App\Framework\Response;

abstract class Response
{
    protected array $headers;
    protected string $encoded;
    protected int $status;

    abstract protected function encode(mixed $data): void;

    protected function send(): void
    {
        foreach ($this->headers as $header) {
            header($header);
        }

        http_response_code($this->status);

        echo($this->encoded);
    }

    public static function from(mixed $data, int $status = 200): void
    {
        $r = new static();

        $r->status = $status;
        $r->encode($data);
        $r->send();
    }
}
