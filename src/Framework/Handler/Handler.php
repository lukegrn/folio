<?php

declare(strict_types=1);

namespace App\Framework\Handler;

abstract class Handler
{
    /**
     * @param array<string, mixed> $args
     */
    abstract protected function handle(array $args): void;

    /**
     * @param array<string,mixed> $args
     */
    public function __invoke(array $args): void
    {
        $this->handle($args);
    }
}
