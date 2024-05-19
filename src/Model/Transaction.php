<?php

namespace App\Model;

class Transaction
{
    const CURRENCY_EUR = 'EUR';

    protected string|null $bin = null;

    protected float $originalAmount = 0;
    protected float $amount = 0;

    protected float $commission = 0;

    protected string|null $originalCurrency = null;
    protected string|null $currency = null;

    protected Country|null $country = null;

    public function __construct(string|null $bin, float $amount, string|null $currency)
    {
        $this->bin = $bin;
        $this->amount = $amount;
        $this->originalAmount = $amount;
        $this->currency = $currency;
        $this->originalCurrency = $currency;
    }

    public function getBin(): ?string
    {
        return $this->bin;
    }

    public function setBin(?string $bin): void
    {
        $this->bin = $bin;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): void
    {
        $this->country = $country;
    }

    public function getCommission(): float
    {
        return $this->commission;
    }

    public function setCommission(float $commission): void
    {
        $this->commission = $commission;
    }

    public function getOriginalAmount(): float
    {
        return $this->originalAmount;
    }

    public function getOriginalCurrency(): ?string
    {
        return $this->originalCurrency;
    }
}
