<?php

namespace Service\TransactionCommission;

use App\Config;
use App\Context;
use App\Model\Country;
use App\Model\Transaction;
use App\Service\BinCountryDetector\Adapter\BinlistAdapter;
use App\Service\BinCountryDetector\BinCountryDetector;
use App\Service\CurrencyRates\Adapter\ExchangeRatesApiAdapter;
use App\Service\CurrencyRates\CurrencyRatesService;
use App\Service\TransactionCommission\TransactionCommissionService;
use App\Service\TransactionProvider\TransactionProvider;
use PHPUnit\Framework\TestCase;

class TransactionCommissionTest extends TestCase
{
    protected $countryMap;

    protected $transactions;

    protected $transactionForErrorTestCase;

    protected $transactionsRaw;

    protected $rates;

    protected $alterDefaultCommission;

    protected $alterEUCommission;

    protected function setUp(): void
    {
        $country = new Country();

        $country->setName('Denmark');
        $country->setAlpha2('DK');
        $country->setNumeric('208');

        $this->countryMap = [
            ['45717360', $country],
        ];

        $this->transactionsRaw = [
            ['bin' => '45717360', 'amount' => '100.00', 'currency' => 'EUR', 'expected' => 1.0, 'expected_alter_commission' => 5.0], # DETECT COUNTRY FROM EU Region ONLY for THIS Transaction
            ['bin' => '516793', 'amount' => '50.00', 'currency' => 'USD', 'expected' => 0.92, 'expected_alter_commission' => 4.59],
            ['bin' => '45417360', 'amount' => '10000.00', 'currency' => 'JPY', 'expected' => 1.18, 'expected_alter_commission' => 5.9],
            ['bin' => '41417360', 'amount' => '130.00', 'currency' => 'USD', 'expected' => 2.39, 'expected_alter_commission' => 11.93],
            ['bin' => '4745030', 'amount' => '2000.00', 'currency' => 'GBP', 'expected' => 46.5, 'expected_alter_commission' => 232.5],
        ];

        foreach ($this->transactionsRaw as $item) {

            $transaction = new Transaction($item['bin'], $item['amount'], $item['currency']);

            $this->transactions[] = $transaction;

            if ($transaction->getAmount() > 100 && $transaction->getCurrency() != 'EUR') {
                $this->transactionForErrorTestCase[] = $transaction;
            }

        }

        $this->alterDefaultCommission = 0.1;
        $this->alterEUCommission = 0.05;

        $this->rates = [
            'AED' => 4.001553,
            'AFN' => 77.872025,
            'ALL' => 100.389719,
            'AMD' => 420.556929,
            'ANG' => 1.9531,
            'AOA' => 924.933623,
            'ARS' => 960.452635,
            'AUD' => 1.625057,
            'AWG' => 1.963713,
            'AZN' => 1.85637,
            'BAM' => 1.9556,
            'BBD' => 2.188176,
            'BDT' => 126.907003,
            'BGN' => 1.9556,
            'BHD' => 0.408658,
            'BIF' => 3109.081583,
            'BMD' => 1.089438,
            'BND' => 1.46055,
            'BOB' => 7.488233,
            'BRL' => 5.558231,
            'BSD' => 1.083789,
            'BTC' => 1.6266097E-5,
            'BTN' => 90.301552,
            'BWP' => 14.684496,
            'BYN' => 3.546037,
            'BYR' => 21352.993132,
            'BZD' => 2.184476,
            'CAD' => 1.474501,
            'CDF' => 3055.875177,
            'CHF' => 0.99348,
            'CLF' => 0.035317,
            'CLP' => 974.500197,
            'CNY' => 7.869781,
            'CNH' => 7.88097,
            'COP' => 4147.675216,
            'CRC' => 554.648196,
            'CUC' => 1.089438,
            'CUP' => 28.870118,
            'CVE' => 110.253708,
            'CZK' => 24.749906,
            'DJF' => 192.980236,
            'DKK' => 7.478564,
            'DOP' => 63.483498,
            'DZD' => 146.392007,
            'EGP' => 51.004776,
            'ERN' => 16.341576,
            'ETB' => 62.248825,
            'EUR' => 1,
            'FJD' => 2.427491,
            'FKP' => 0.867301,
            'GBP' => 0.86023,
            'GEL' => 2.996381,
            'GGP' => 0.867301,
            'GHS' => 15.497413,
            'GIP' => 0.867301,
            'GMD' => 73.977064,
            'GNF' => 9315.045999,
            'GTQ' => 8.420138,
            'GYD' => 226.736779,
            'HKD' => 8.498931,
            'HNL' => 26.833252,
            'HRK' => 7.604232,
            'HTG' => 144.135238,
            'HUF' => 387.986613,
            'IDR' => 17392.339738,
            'ILS' => 4.03589,
            'IMP' => 0.867301,
            'INR' => 90.748968,
            'IQD' => 1419.654606,
            'IRR' => 45824.507913,
            'ISK' => 150.65886,
            'JEP' => 0.867301,
            'JMD' => 169.122679,
            'JOD' => 0.772307,
            'JPY' => 169.565687,
            'KES' => 141.425516,
            'KGS' => 96.098605,
            'KHR' => 4419.547372,
            'KMF' => 493.216054,
            'KPW' => 980.494253,
            'KRW' => 1475.524926,
            'KWD' => 0.33472,
            'KYD' => 0.903207,
            'KZT' => 481.870649,
            'LAK' => 23131.630973,
            'LBP' => 97047.660859,
            'LKR' => 324.806635,
            'LRD' => 210.810445,
            'LSL' => 19.839089,
            'LTL' => 3.216829,
            'LVL' => 0.658991,
            'LYD' => 5.238364,
            'MAD' => 10.790195,
            'MDL' => 19.176086,
            'MGA' => 4802.508151,
            'MKD' => 61.603691,
            'MMK' => 2275.866917,
            'MNT' => 3758.56232,
            'MOP' => 8.709608,
            'MRU' => 43.254784,
            'MUR' => 49.864043,
            'MVR' => 16.84313,
            'MWK' => 1879.007561,
            'MXN' => 17.962703,
            'MYR' => 5.106747,
            'MZN' => 69.183403,
            'NAD' => 19.839084,
            'NGN' => 1601.474859,
            'NIO' => 39.895914,
            'NOK' => 11.701277,
            'NPR' => 144.482203,
            'NZD' => 1.77621,
            'OMR' => 0.418497,
            'PAB' => 1.083689,
            'PEN' => 4.047186,
            'PGK' => 4.208569,
            'PHP' => 63.024421,
            'PKR' => 301.871884,
            'PLN' => 4.265063,
            'PYG' => 8120.168373,
            'QAR' => 3.967194,
            'RON' => 4.985492,
            'RSD' => 117.119305,
            'RUB' => 99.019465,
            'RWF' => 1403.056306,
            'SAR' => 4.085834,
            'SBD' => 9.234021,
            'SCR' => 15.053902,
            'SDG' => 654.752863,
            'SEK' => 11.675495,
            'SGD' => 1.465336,
            'SHP' => 1.376451,
            'SLE' => 24.89073,
            'SLL' => 22844.980079,
            'SOS' => 622.069709,
            'SRD' => 35.061438,
            'STD' => 22549.175832,
            'SVC' => 9.483029,
            'SYP' => 2737.246569,
            'SZL' => 19.82197,
            'THB' => 39.568806,
            'TJS' => 11.736798,
            'TMT' => 3.813034,
            'TND' => 3.393642,
            'TOP' => 2.569604,
            'TRY' => 35.127203,
            'TTD' => 7.359246,
            'TWD' => 35.057915,
            'TZS' => 2812.311977,
            'UAH' => 42.762021,
            'UGX' => 4085.581575,
            'USD' => 1.089438,
            'UYU' => 41.9857,
            'UZS' => 13784.588251,
            'VEF' => 3946547.927976,
            'VES' => 39.852469,
            'VND' => 27725.118481,
            'VUV' => 129.340298,
            'WST' => 3.053797,
            'XAF' => 655.889827,
            'XAG' => 0.034656,
            'XAU' => 0.000451,
            'XCD' => 2.944262,
            'XDR' => 0.820516,
            'XOF' => 655.889827,
            'XPF' => 119.331742,
            'YER' => 272.254574,
            'ZAR' => 19.69095,
            'ZMK' => 9806.257059,
            'ZMW' => 27.662167,
            'ZWL' => 350.798728,
        ];
    }

    public function testSuccessCommissionApplied()
    {
        $service = $this->getTransactionCommissionService();

        foreach ($this->transactions as $i => $transaction) {
            $service->applyCommission($transaction);

            $this->assertEquals($this->transactionsRaw[$i]['expected'], $transaction->getCommission());
        }
    }

    public function testSuccessCommissionAppliedWithOtherCommissionRates()
    {
        $service = $this->getTransactionCommissionService(true);

        foreach ($this->transactions as $i => $transaction) {
            $service->applyCommission($transaction);

            $this->assertEquals($this->transactionsRaw[$i]['expected_alter_commission'], $transaction->getCommission());
        }
    }

    public function testErrorCommissionAppliedIfCurrencyRatesEmpty()
    {
        $service = $this->getTransactionCommissionService(false, true);

        foreach ($this->transactionForErrorTestCase as $i => $transaction) {
            $service->applyCommission($transaction);
            $this->assertNotEquals($this->transactionsRaw[$i]['expected'], $transaction->getCommission());
        }

        $serviceWithAlterCommission = $this->getTransactionCommissionService(false, true);

        foreach ($this->transactionForErrorTestCase as $i => $transaction) {
            $serviceWithAlterCommission->applyCommission($transaction);
            $this->assertNotEquals($this->transactionsRaw[$i]['expected'], $transaction->getCommission());
        }
    }

    protected function getBinCountryDetector(): BinCountryDetector
    {
        $res = $this->createMock(BinCountryDetector::class);

        $res->method('detectCountry')->willReturnMap($this->countryMap);

        return $res;
    }

    protected function getCurrencyRateService($emptyRates = false): CurrencyRatesService
    {
        $config = Config::getInstance()->getConfig();

        $adapter = $this->createMock(ExchangeRatesApiAdapter::class);

        $adapter->method('getExchangeRates')->willReturn($emptyRates ? [] : $this->rates);

        $config['services'][CurrencyRatesService::class]['params']['adapter']['class'] = $adapter;

        Config::getInstance()->setConfig($config);

        return Context::getService(CurrencyRatesService::class, true);
    }

    protected function getTransactionCommissionService($alterCommission = false, $emptyRates = false): TransactionCommissionService
    {
        $config = Config::getInstance()->getConfig();

        if ($alterCommission) {
            $config['services'][TransactionCommissionService::class]['params']['commission_rates']['eu'] = $this->alterEUCommission;
            $config['services'][TransactionCommissionService::class]['params']['commission_rates']['default'] = $this->alterDefaultCommission;
        }

        $config['services'][TransactionCommissionService::class]['params']['bin_country_detector'] = $this->getBinCountryDetector();
        $config['services'][TransactionCommissionService::class]['params']['currency_rates_service'] = $this->getCurrencyRateService($emptyRates);

        Config::getInstance()->setConfig($config);

        return Context::getService(TransactionCommissionService::class, true);
    }
}
