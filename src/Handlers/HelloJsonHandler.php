<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Framework\Handler\Handler;
use App\Framework\Response\JsonResponse;

class HelloJsonHandler extends Handler
{
    protected function handle(array $args): void
    {
        JsonResponse::from(['message' => 'Hello, json!'], 200);
    }
}
