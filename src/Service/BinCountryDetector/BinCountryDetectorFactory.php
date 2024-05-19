<?php

namespace App\Service\BinCountryDetector;

use App\Config;
use App\FactoryInterface;
use App\Service\BinCountryDetector\Adapter\BinCountryDetectorAdapterInterface;

class BinCountryDetectorFactory implements FactoryInterface
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

        if (!$adapter instanceof BinCountryDetectorAdapterInterface) {
            throw new \UnexpectedValueException('Class does not implement BinCountryDetectorAdapterInterface.');
        }

        return new BinCountryDetector($adapter);
    }
}
