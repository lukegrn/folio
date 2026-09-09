<?php

declare(strict_types=1);

use App\Framework\Config\Config;
use App\Framework\DB\DB;
use PHPUnit\Framework\TestCase;

final class DBTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // TODO: Eventually have a test config to enable this running in a different db
        Config::set("./conf.ini");

        DB::setUp(
            Config::get()->dbHost,
            Config::get()->dbPort,
            Config::get()->dbName,
            Config::get()->dbUser,
            Config::get()->dbPass,
        );

        DB::DB()->exec("CREATE TABLE testing_db_test (val char(5))");
    }


    public function testCanInsertData(): void
    {
        DB::DB()->exec("INSERT INTO testing_db_test VALUES ('test')");
        DB::DB()->exec("INSERT INTO testing_db_test VALUES ('one')");
        DB::DB()->exec("INSERT INTO testing_db_test VALUES ('other')");

        $this->expectNotToPerformAssertions();
    }

    public function testCanQueryData(): void
    {
        $stmt = DB::DB()->query("SELECT * FROM testing_db_test");
        $results = $stmt->fetchAll();

        $this->assertEquals("test ", $results[0][0]);
    }

    public static function tearDownAfterClass(): void
    {
        DB::DB()->exec("DROP TABLE testing_db_test");
    }
}
