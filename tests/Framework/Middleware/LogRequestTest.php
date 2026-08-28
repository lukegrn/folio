<?php

declare(strict_types=1);

use App\Framework\Middleware\LogRequest;
use App\Framework\Handler\Handler;
use PhpUnit\Framework\TestCase;

class LogRequestTestHandler extends Handler
{
    protected function handle(array $args): void
    {
    }
}

final class LogRequestTest extends TestCase
{
    public function testLogsExpectedData(): void
    {
        $_SERVER['REQUEST_URI'] = '';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $l = new LogRequest(new LogRequestTestHandler());
        $l->setOutput('php://output');

        $this->expectOutputRegex('/^\{"path":"","verb":"GET","duration":\d+,"response":\{"status":false\},"time":"\d{4}-[01]{1}\d{1}-[0123]{1}\d{1} [012]{1}\d{1}:[0-6]{1}\d{1}:[0-6]{1}\d{1} UTC"\}$/');
        $l([]);
    }
}
