<?php

namespace App;

class Config
{
    const DEFAULT_CONFIG_PATH = __DIR__ . '/../config/main.php';
    const RUNTIME_PATH = __DIR__ . '/../runtime';

    private static self|null $instance = null;

    private array $config = [];

    private function __construct(array $config)
    {
        $this->config = $config;
    }

    public static function getInstance(): Config
    {
        if (!static::$instance instanceof static) {
            static::$instance = new static(require_once self::DEFAULT_CONFIG_PATH);
        }

        return static::$instance;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function get(mixed $key, mixed $default = null): mixed
    {
        $res = null;

        if (is_array($key)) {
            $pointer = $this->config;
            while (null !== ($tmp = array_shift($key))) {
                if (!array_key_exists($tmp, $pointer)) {
                    return $default;
                }

                $pointer = &$pointer[$tmp];
            }
            $res = $pointer;
        } else {
            $res = $this->config[$key] ?? $default;
        }

        return $res;
    }
}
