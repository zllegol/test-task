<?php

namespace App\Service\CurrencyRates;

use App\FactoryInterface;
use App\Service\CurrencyRates\Adapter\CurrencyRatesAdapterInterface;

class CurrencyRatesServiceFactory implements FactoryInterface
{
    public function build($params = [])
    {
        $adapterConfig = $params['adapter'] ?? [];

        $adapterClass = $adapterConfig['class'] ?? '';

        if (is_string($adapterClass)) {

            if (!class_exists($adapterClass)) {
                throw new \UnexpectedValueException('Class impl is missing - ' . $adapterClass);
            }

            $adapterParams = $adapterConfig['params'] ?? [];

            $adapter = new $adapterClass($adapterParams);

        } elseif (is_object($adapterClass)) {
            $adapter = $adapterClass;
        }

        if (!$adapter instanceof CurrencyRatesAdapterInterface) {
            throw new \UnexpectedValueException('Class does not implement CurrencyRatesAdapterInterface.');
        }

        return new CurrencyRatesService($adapter);
    }
}
