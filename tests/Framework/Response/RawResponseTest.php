<?php

declare(strict_types=1);

use App\Framework\Response\RawResponse;
use PHPUnit\Framework\TestCase;

final class RawResponseTest extends TestCase
{
    protected function tearDown(): void
    {
        foreach (headers_list() as $header) {
            header_remove($header);
        }
    }

    public function testOutputsData(): void
    {
        $this->expectOutputString("Test output");
        RawResponse::from("Test output", 200);
    }

    public function testSetsHeaders(): void
    {
        RawResponse::from("", 200);

        $this->assertContains('Content-Type: text/html; charset=utf-8', xdebug_get_headers());
    }

    public function testSetsResponseCode(): void
    {
        RawResponse::from("", 401);

        $this->assertEquals(http_response_code(), 401);
    }
}
