<?php

declare(strict_types=1);

namespace App\Framework\DB;

class DB
{
    private \PDO $db;
    private static DB $inst;

    private function __construct(
        string $host,
        string $port,
        string $db,
        string $user,
        string $pass
    ) {
        $this->db = new \PDO("pgsql:host=$host;port=$port;dbname=$db;", $user, $pass, [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }

    public static function setUp(
        string $host,
        string $port,
        string $db,
        string $user,
        string $pass
    ): void {
        if (!isset(static::$inst)) {
            static::$inst = new static($host, $port, $db, $user, $pass);
        }
    }

    public static function DB(): \PDO
    {
        return static::$inst->db;
    }
}
