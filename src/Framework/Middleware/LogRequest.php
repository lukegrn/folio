<?php

declare(strict_types=1);

namespace App\Framework\Middleware;

use App\Framework\Handler\Handler;

class LogRequest extends Middleware
{
    protected static $output;

    protected function handle(array $args): void
    {
        $start = hrtime(true);

        $payload = [
            'path' => $_SERVER['REQUEST_URI'],
            'verb' => $_SERVER['REQUEST_METHOD']
        ];

        ($this->next)($args);

        $payload['duration'] = hrtime(true) - $start;
        $payload['response'] = [
            'status' => http_response_code()
        ];
        $payload['time'] = date('Y-m-d H:i:s e');

        file_put_contents(static::$output, json_encode($payload) . "\n", FILE_APPEND);
    }

    public static function setOutput(string $output): void
    {
        static::$output = $output;
    }

    public function __construct(Handler $next)
    {
        parent::__construct($next);

        // Set default output
        if (!isset(static::$output)) {
            static::$output = 'php://stdout';
        }
    }
}
