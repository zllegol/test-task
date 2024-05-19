<?php

namespace App\Service\BinCountryDetector\Adapter;

use App\Model\Country;

interface BinCountryDetectorAdapterInterface
{
    public function detectCountry(string $bin): array;
}
