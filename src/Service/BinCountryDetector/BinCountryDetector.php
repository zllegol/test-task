<?php

namespace App\Service\BinCountryDetector;

use App\Model\Country;
use App\Service\BinCountryDetector\Adapter\BinCountryDetectorAdapterInterface;

class BinCountryDetector
{
    protected BinCountryDetectorAdapterInterface $adapter;

    public function __construct(BinCountryDetectorAdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function detectCountry(string $bin): ?Country
    {
        $res = null;

        if ($country = $this->adapter->detectCountry($bin)) {
            $res = new Country();

            $res->setName($country['name']);
            $res->setAlpha2($country['alpha2']);
            $res->setNumeric($country['numeric']);
        }

        return $res;
    }
}
