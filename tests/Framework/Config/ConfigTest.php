<?php

declare(strict_types=1);

use App\Framework\Config\Config;
use App\Framework\Config\Exceptions\ConfigAlreadySetUpException;
use App\Framework\Config\Exceptions\ConfigFileNotFoundException;
use App\Framework\Config\Exceptions\ConfigNotSetUpException;
use App\Framework\Config\Exceptions\ConfigValueNotSetException;
use App\Framework\Config\Exceptions\MalformedConfigFileException;
use App\Framework\Config\Exceptions\RequiredConfigValueNotFoundException;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    private const PATH = "/tmp/conf.ini";
    private const PATH_FOR_MALFORMED = "/tmp/malformed.ini";
    private const CONF_VALUES = "test = value\nother = othervalue\n";
    private const MALFORMED_CONF_VALUES = "= value\nother = othervalue\n";

    protected function setUp(): void
    {
        $f = fopen(self::PATH, 'w');
        fwrite($f, self::CONF_VALUES);
        fclose($f);

        $f = fopen(self::PATH_FOR_MALFORMED, 'w');
        fwrite($f, self::MALFORMED_CONF_VALUES);
        fclose($f);

        parent::setUp();
    }

    protected function tearDown(): void
    {
        $reflector = new \ReflectionClass(Config::class);
        $property = $reflector->getProperty('instance');
        $property->setValue(null, null);

        unlink(self::PATH);
        unlink(self::PATH_FOR_MALFORMED);

        parent::tearDown();
    }

    public function testCanCreateFromPath(): void
    {
        Config::set(self::PATH);
        $this->assertInstanceOf(Config::class, Config::get());
    }

    public function testThrowsErrorWhenFileNotFound(): void
    {
        $this->expectException(ConfigFileNotFoundException::class);
        Config::set("/bad/path");
    }

    public function testCanGetValueFromConfig(): void
    {
        Config::set(self::PATH);
        $this->assertEquals("value", Config::get()->test);
    }

    public function testGettingInvalidValueFromConfigThrowsError(): void
    {
        Config::set(self::PATH);
        $this->expectException(ConfigValueNotSetException::class);
        Config::get()->unsetConfValue;
    }

    public function testCanRequireFoundValue(): void
    {
        Config::set(self::PATH);
        Config::require('test');

        $this->expectNotToPerformAssertions();
    }

    public function testRequiredValueThrowsErrorWhenNotFound(): void
    {
        Config::set(self::PATH);
        $this->expectException(RequiredConfigValueNotFoundException::class);
        Config::require('valueNotSet');
    }

    public function testThrowsErrorWhenConfigHasNotBeenSetUp(): void
    {
        $this->expectException(ConfigNotSetUpException::class);
        Config::get();
    }

    public function testThrowsErrorWhenTryingToSetUpAlreadySetUp(): void
    {
        Config::set(self::PATH);
        $this->expectException(ConfigAlreadySetUpException::class);
        Config::set(self::PATH);
    }

    public function testFailureToParseINIThrowsError(): void
    {
        $this->expectException(MalformedConfigFileException::class);
        Config::set(self::PATH_FOR_MALFORMED);
    }
}
