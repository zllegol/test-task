<?php

namespace App\Service\TransactionProvider;

use App\FactoryInterface;
use App\Service\TransactionProvider\Provider\ProviderInterface;

class TransactionProviderFactory implements FactoryInterface
{
    public function build($params = [])
    {
        $providerConfig = $params['provider'] ?? [];

        $providerClass = $providerConfig['class'] ?? '';

        if (is_string($providerClass)) {

            if (!class_exists($providerClass)) {
                throw new \UnexpectedValueException('Class impl is missing - ' . $providerClass);
            }

            $provider = new $providerClass();

        } elseif (is_object($providerClass)) {
            $provider = $providerClass;
        }

        if (!$provider instanceof ProviderInterface) {
            throw new \UnexpectedValueException('Class does not implement ProviderInterface.');
        }

        return new TransactionProvider($provider);
    }
}
