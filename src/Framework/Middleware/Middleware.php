<?php

declare(strict_types=1);

namespace App\Framework\Middleware;

use App\Framework\Handler\Handler;

abstract class Middleware extends Handler
{
    protected Handler $next;

    /**
     * @param Handler $next
     */
    public function __construct(Handler $next)
    {
        $this->next = $next;
    }
}
