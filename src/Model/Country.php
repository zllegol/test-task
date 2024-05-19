<?php

namespace App\Model;

class Country
{
    const AT = 'at';
    const BE = 'be';
    const BG = 'BG';
    const CY = 'CY';
    const CZ = 'CZ';
    const DE = 'DE';
    const DK = 'DK';
    const EE = 'EE';
    const ES = 'ES';
    const FI = 'FI';
    const FR = 'FR';
    const GB = 'GB';
    const HR = 'HR';
    const HU = 'HU';
    const IE = 'IE';
    const IT = 'IT';
    const LT = 'LT';
    const LU = 'LU';
    const LV = 'LV';
    const MT = 'MT';
    const NL = 'NL';
    const PO = 'PO';
    const PT = 'PT';
    const RO = 'RO';
    const SE = 'SE';
    const SI = 'SI';
    const SK = 'SK';

    const EU_CODE_LIST = [
        self::AT, self::BE, self::BG, self::CY,
        self::CZ, self::DE, self::DK, self::EE, self::ES,
        self::FI, self::FR, self::GB, self::HR, self::HU, self::IE, self::IT,
        self::LT, self::LU, self::LV, self::MT, self::NL, self::PO, self::PT, self::RO,
        self::SE, self::SI, self::SK,
    ];
    protected string|null $alpha2 = null;

    protected string|null $name = null;

    protected string|null $numeric = null;

    public function getAlpha2(): ?string
    {
        return $this->alpha2;
    }

    public function setAlpha2(?string $alpha2): void
    {
        $this->alpha2 = $alpha2;
    }

    public function isEURegion(): bool
    {
        return in_array($this->alpha2, self::EU_CODE_LIST);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getNumeric(): ?string
    {
        return $this->numeric;
    }

    public function setNumeric(?string $numeric): void
    {
        $this->numeric = $numeric;
    }
}
