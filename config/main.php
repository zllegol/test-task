<?php

return [
    'services' => [
        \App\Service\CurrencyRates\CurrencyRatesService::class => [
            'class'  => \App\Service\CurrencyRates\CurrencyRatesServiceFactory::class,
            'params' => [
                'adapter' => [
                    'class' => \App\Service\CurrencyRates\Adapter\ExchangeRatesApiAdapter::class,
                    'params' => [
                        'access_key' => '#PUT_YOUR_ACCESS_KEY#',
                    ]
                ]
            ]
        ],

        \App\Service\BinCountryDetector\BinCountryDetector::class => [
            'class' => \App\Service\BinCountryDetector\BinCountryDetectorFactory::class,
            'params' => [
                'adapter' => [
                    'class' => \App\Service\BinCountryDetector\Adapter\BinlistAdapter::class
                ]
            ]
        ],

        \App\Service\TransactionProvider\TransactionProvider::class => [
            'class'  => \App\Service\TransactionProvider\TransactionProviderFactory::class,
            'params' => [
                'provider' => [
                    'class' => \App\Service\TransactionProvider\Provider\JsonProvider::class,
                ]
            ]
        ],

        \App\Service\TransactionCommission\TransactionCommissionService::class => [
            'class' => \App\Service\TransactionCommission\TransactionCommissionServiceFactory::class,
            'params' => [
                'commission_rates' => [
                    'default' => 0.02,
                    'eu'      => 0.01,
                ]
            ]
        ],
    ],
];
