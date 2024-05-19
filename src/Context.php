<?php

namespace App;

class Context
{
    protected static array $services = [];

    public static function getService(string $name, bool $force = false)
    {
        if ($force || !array_key_exists($name, self::$services)) {
            $config = Config::getInstance();

            $factoryConfig = $config->get(['services', $name]);

            $factoryClassname = ($factoryConfig ? $factoryConfig['class'] ?? '' : '');

            if (!class_exists($factoryClassname)) {
                throw new \UnexpectedValueException('Cant create service ' . $name);
            }

            $factory = new $factoryClassname();

            if (!$factory instanceof FactoryInterface) {
                throw new \UnexpectedValueException('Must be factory interface');
            }

            $factoryParams = ($factoryConfig ? $factoryConfig['params'] ?? [] : []);

            self::$services[$name] = $factory->build($factoryParams);
        }

        return self::$services[$name];
    }
}
