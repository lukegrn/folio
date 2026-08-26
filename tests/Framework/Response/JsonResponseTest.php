<?php

declare(strict_types=1);

use App\Framework\Response\JsonResponse;
use PHPUnit\Framework\TestCase;

final class JsonResponseTest extends TestCase
{
    protected function tearDown(): void
    {
        foreach (headers_list() as $header) {
            header_remove($header);
        }
    }

    public function testOutputsStringPrimitives(): void
    {
        $this->expectOutputString('"Test output"');
        JsonResponse::from("Test output", 200);
    }

    public function testOutputsIntPrimitives(): void
    {
        $this->expectOutputString("1");
        JsonResponse::from(1, 200);
    }

    public function testOutputsWellFormedJson(): void
    {
        $this->expectOutputString('{"well":"formed","array":[1,2]}');
        JsonResponse::from(['well' => 'formed', 'array' => [1, 2]]);
    }

    public function testEncodesEmptyString(): void
    {
        $this->expectOutputString('""');
        JsonResponse::from("", 200);
    }

    public function testSetsHeaders(): void
    {
        $this->expectOutputString('""');
        JsonResponse::from("", 200);

        $this->assertContains('Content-Type: application/json; charset=utf-8', xdebug_get_headers());
    }

    public function testSetsResponseCode(): void
    {
        $this->expectOutputString('""');
        JsonResponse::from("", 401);

        $this->assertEquals(http_response_code(), 401);
    }

    public function testMalformedJson500s(): void
    {
        $this->expectOutputString('{"message": "error encoding"}');
        JsonResponse::from("\x61\xf0\x80\x80\x41", 200);
        $this->assertEquals(http_response_code(), 500);
    }
}
