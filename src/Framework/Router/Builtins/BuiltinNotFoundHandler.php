<?php

declare(strict_types=1);

namespace App\Framework\Router\Builtins;

use App\Framework\Handler\Handler;
use App\Framework\Response\RawResponse;

class BuiltinNotFoundHandler extends Handler
{
    protected function handle(array $args): void
    {
        RawResponse::from("Not found", 404);
    }
}
