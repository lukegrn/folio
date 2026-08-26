<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Framework\Handler\Handler;
use App\Framework\Response\RawResponse;

class HelloHandler extends Handler
{
    protected function handle(array $args): void
    {
        $helloStr = 'Hello, ';

        if (array_key_exists('name', $args)) {
            $helloStr .= $args['name'];
        } else {
            $helloStr .= 'World';
        }

        $helloStr .= '!';

        RawResponse::from($helloStr, 200);
    }
}
