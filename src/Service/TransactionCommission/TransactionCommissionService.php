<?php

namespace App\Service\TransactionCommission;

use App\Model\Transaction;
use App\Service\BinCountryDetector\BinCountryDetector;
use App\Service\CurrencyRates\CurrencyRatesService;

class TransactionCommissionService
{
    protected BinCountryDetector $binCountryDetector;

    protected CurrencyRatesService $currencyRatesService;

    protected float $defaultCommissionRate = 0;
    protected float $euCommissionRate = 0;

    public function __construct(BinCountryDetector $binCountryDetector, CurrencyRatesService $currencyRatesService, float $defaultCommissionRate, float $euCommissionRate)
    {
        $this->binCountryDetector = $binCountryDetector;
        $this->currencyRatesService = $currencyRatesService;
        $this->defaultCommissionRate = $defaultCommissionRate;
        $this->euCommissionRate = $euCommissionRate;
    }

    public function applyCommission(Transaction $transaction)
    {
        if (!$transaction->getCountry()) {
            $country = $this->binCountryDetector->detectCountry($transaction->getBin());
            $transaction->setCountry($country);
        }

        $amount = $this->currencyRatesService->exchange($transaction->getOriginalCurrency(), Transaction::CURRENCY_EUR, $transaction->getOriginalAmount());

        $transaction->setAmount($amount);
        $transaction->setCurrency(Transaction::CURRENCY_EUR);

        $commissionRate = $this->defaultCommissionRate;

        if ($transaction->getCountry() && $transaction->getCountry()->isEURegion()) {
            $commissionRate = $this->euCommissionRate;
        }

        $commission = $amount * $commissionRate;
        $commission = round($commission, 2);

        $transaction->setCommission($commission);
    }
}
