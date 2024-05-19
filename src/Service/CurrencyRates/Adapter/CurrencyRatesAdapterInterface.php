<?php

namespace App\Service\CurrencyRates\Adapter;

interface CurrencyRatesAdapterInterface
{
    public function getExchangeRates(string $currency): array;

}
