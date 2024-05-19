<?php

namespace App\Service\TransactionCommission;

use App\Context;
use App\FactoryInterface;
use App\Service\BinCountryDetector\BinCountryDetector;
use App\Service\CurrencyRates\CurrencyRatesService;

class TransactionCommissionServiceFactory implements FactoryInterface
{
    public function build($params = [])
    {
        $commissionConfig = $params['commission_rates'];

        $defaultCommission = $commissionConfig['default'] ?? 0;
        $euCommission = $commissionConfig['eu'] ?? 0;

        $binCountryDetector = $params['bin_country_detector'] ?? Context::getService(BinCountryDetector::class);
        $currencyRatesService = $params['currency_rates_service'] ?? Context::getService(CurrencyRatesService::class);

        return new TransactionCommissionService($binCountryDetector, $currencyRatesService, $defaultCommission, $euCommission);
    }
}
