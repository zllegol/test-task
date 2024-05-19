<?php

namespace Service\BinCountryDetector;

use App\Config;
use App\Context;
use App\Model\Country;
use App\Service\BinCountryDetector\Adapter\BinlistAdapter;
use App\Service\BinCountryDetector\BinCountryDetector;
use App\Service\TransactionProvider\Provider\JsonProvider;
use App\Service\TransactionProvider\TransactionProvider;
use PHPUnit\Framework\TestCase;

class BinCountryDetectorTest extends TestCase
{
    protected $bin;
    protected $country;

    protected $countryRaw;

    protected function setUp(): void
    {
        $this->bin = '45717360';

        $this->country = new Country();
        $this->country->setName('Denmark');
        $this->country->setAlpha2('DK');
        $this->country->setNumeric('208');

        $this->countryRaw = [
            'name'    => 'Denmark',
            'alpha2'  => 'DK',
            'numeric' => '208'
        ];
    }

    public function testSuccessCountryDetection()
    {
        $binCountryDetector = $this->getBinCountryDetector($this->bin);

        $country = $binCountryDetector->detectCountry($this->bin);

        $this->assertInstanceOf(Country::class, $country);

        $this->assertEquals($this->country->getName(), $country->getName());
        $this->assertEquals($this->country->getNumeric(), $country->getNumeric());
        $this->assertEquals($this->country->getAlpha2(), $country->getAlpha2());
    }

    public function testFailureCountryDetection()
    {
        $binCountryDetector = $this->getBinCountryDetector('0000000');

        $country = $binCountryDetector->detectCountry('0000000');

        $this->assertEquals(null, $country);
    }

    protected function getBinCountryDetector($bin): BinCountryDetector
    {
        $config = Config::getInstance()->getConfig();

        $adapter = $this->createMock(BinlistAdapter::class);

        if ($this->bin == $bin) {
            $adapter->method('detectCountry')->willReturn($this->countryRaw);
        } else {
            $adapter->method('detectCountry')->willReturn([]);
        }

        $config['services'][BinCountryDetector::class]['params']['adapter']['class'] = $adapter;

        Config::getInstance()->setConfig($config);

        return Context::getService(BinCountryDetector::class, true);
    }
}
