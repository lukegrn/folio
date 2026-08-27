<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Framework\Router\Builtins\BuiltinNotFoundHandler;

final class BuiltinNotFoundHandlerTest extends TestCase
{
    public function testStatusIs404AndMessageIsCorrect(): void
    {
        $h = new BuiltinNotFoundHandler();
        $this->expectOutputString('Not found');
        $h([]);
        $this->assertEquals(http_response_code(), 404);
    }
}
