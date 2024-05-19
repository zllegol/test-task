<?php

namespace App\Service\CurrencyRates;

use App\Service\CurrencyRates\Adapter\CurrencyRatesAdapterInterface;

class CurrencyRatesService
{
    protected CurrencyRatesAdapterInterface $adapter;

    /** @var array Cached rates */
    protected array $rates = [];

    public function __construct(CurrencyRatesAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function exchange(string $from, string $to, float $amount): float
    {
        $res = 0;

        if ($from === $to) {
            $res = $amount;
        } else {
            $rates = $this->getRates($to);

            $rate = $rates[$from] ?? 0;

            $res = $rate ? $amount / $rate : $amount;
        }

        return round($res, 2);
    }

    protected function getRates(string $currency): array
    {
        if (!array_key_exists($currency, $this->rates)) {
            $this->rates[$currency] = $this->adapter->getExchangeRates($currency);
        }

        return $this->rates[$currency];
    }
}
