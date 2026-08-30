<?php

declare(strict_types=1);

namespace App\Framework\Config;

use App\Framework\Config\Exceptions\ConfigAlreadySetUpException;
use App\Framework\Config\Exceptions\ConfigFileNotFoundException;
use App\Framework\Config\Exceptions\ConfigNotSetUpException;
use App\Framework\Config\Exceptions\ConfigValueNotSetException;
use App\Framework\Config\Exceptions\MalformedConfigFileException;
use App\Framework\Config\Exceptions\RequiredConfigValueNotFoundException;

class Config
{
    private array $config;
    private static ?Config $instance;

    /**
     * @param string $path
     */
    private function __construct(string $path)
    {
        if (!file_exists($path)) {
            throw new ConfigFileNotFoundException("Config file at path $path not found");
        }

        $result = parse_ini_file($path);

        if (!$result) {
            throw new MalformedConfigFileException("Failed to parse config file $path");
        }

        $this->config = parse_ini_file($path);
    }

    /**
     * @param string $v the config value to get
     */
    public function __get(string $v): mixed
    {
        if (!array_key_exists($v, $this->config)) {
            throw new ConfigValueNotSetException("Config value $v not set");
        }

        return $this->config[$v];
    }

    /**
     * @param string $v the config value to get
     * Throw a fatal error if the value is not preset
     */
    public static function require(string $v): void
    {
        if (!array_key_exists($v, static::$instance->config)) {
            throw new RequiredConfigValueNotFoundException("Required config value $v not found in config");
        }
    }

    /**
     * @param string $path
     */
    public static function set(string $path): void
    {
        if (isset(static::$instance)) {
            throw new ConfigAlreadySetUpException("Cannot set up config once it has been initialized");
        }

        static::$instance = new static($path);
    }

    public static function get(): Config
    {
        if (!isset(static::$instance)) {
            throw new ConfigNotSetUpException('Config has not been initialized. Config::set($path) must be called before Config::get()');
        }
        return static::$instance;
    }
}
