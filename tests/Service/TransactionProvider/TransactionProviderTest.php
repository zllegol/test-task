<?php

namespace Service\TransactionProvider;

use App\Config;
use App\Context;
use App\Service\TransactionProvider\Provider\JsonProvider;
use App\Service\TransactionProvider\TransactionProvider;
use PHPUnit\Framework\TestCase;

class TransactionProviderTest extends TestCase
{
    protected $transactionsJSON = [];
    protected $transactionsRawJSON;

    protected function setUp(): void
    {
        $this->transactionsRawJSON = '{"bin":"45717360","amount":"100.00","currency":"EUR"}
{"bin":"516793","amount":"50.00","currency":"USD"}
{"bin":"45417360","amount":"10000.00","currency":"JPY"}
{"bin":"41417360","amount":"130.00","currency":"USD"}
{"bin":"4745030","amount":"2000.00","currency":"GBP"}';

        $this->transactionsJSON = [
            ['bin' => '45717360', 'amount' => '100.00', 'currency' => 'EUR'],
            ['bin' => '516793', 'amount' => '50.00', 'currency' => 'USD'],
            ['bin' => '45417360', 'amount' => '10000.00', 'currency' => 'JPY'],
            ['bin' => '41417360', 'amount' => '130.00', 'currency' => 'USD'],
            ['bin' => '4745030', 'amount' => '2000.00', 'currency' => 'GBP'],
        ];
    }

    public function testSuccessServiceCreated()
    {
        $this->assertEquals(TransactionProvider::class, get_class(Context::getService(TransactionProvider::class)));
    }

    public function testSuccessServiceFromJSONCreated()
    {
        $this->assertEquals(TransactionProvider::class, get_class($this->getTransactionProviderFromJSON()));
    }

    public function testSuccessTransactionProvidedFromJSON()
    {
        $tmpFilepath = Config::RUNTIME_PATH . '/' . uniqid();

        file_put_contents($tmpFilepath, $this->transactionsRawJSON);

        $provider = $this->getTransactionProviderFromJSON();

        $transactions = $provider->getTransactions(['filepath' => $tmpFilepath]);

        $this->assertEquals(count($this->transactionsJSON), count($transactions));

        foreach ($transactions as $i => $transaction) {
            $actualTransaction = $this->transactionsJSON[$i];

            $this->assertEquals($actualTransaction['bin'], $transaction->getBin());
            $this->assertEquals($actualTransaction['amount'], $transaction->getAmount());
            $this->assertEquals($actualTransaction['currency'], $transaction->getCurrency());

            $this->assertEquals($actualTransaction['amount'], $transaction->getOriginalAmount());
            $this->assertEquals($actualTransaction['currency'], $transaction->getOriginalCurrency());
        }

        unlink($tmpFilepath);
    }

    public function testFailureTransactionProvidedFromJSON()
    {
        $provider = $this->getTransactionProviderFromJSON();

        $this->expectException(\UnexpectedValueException::class);

        $provider->getTransactions(['filepath' => '']);
    }

    public function testBadInputProvidedFromJSON()
    {
        $tmpFilepath = Config::RUNTIME_PATH . '/' . uniqid();

        file_put_contents($tmpFilepath, 'asddasdasadsadsasd');

        /** @var TransactionProvider $provider */
        $provider = Context::getService(TransactionProvider::class);

        $transactions = $provider->getTransactions(['filepath' => $tmpFilepath]);

        $this->assertEquals(0, count($transactions));

        unlink($tmpFilepath);

        $tmpFilepath = Config::RUNTIME_PATH . '/' . uniqid();

        file_put_contents($tmpFilepath, $this->transactionsRawJSON . 'zcfafafa');

        $provider = $this->getTransactionProviderFromJSON();

        $transactions = $provider->getTransactions(['filepath' => $tmpFilepath]);

        $actualTransactions = $this->transactionsJSON;

        array_pop($actualTransactions);

        $this->assertEquals(count($actualTransactions), count($transactions));

        unlink($tmpFilepath);
    }

    protected function getTransactionProviderFromJSON(): TransactionProvider
    {
        $config = Config::getInstance()->getConfig();

        $config['services'][TransactionProvider::class]['adapter']['class'] = JsonProvider::class;

        Config::getInstance()->setConfig($config);

        /** @var TransactionProvider $provider */
        $provider = Context::getService(TransactionProvider::class);

        return $provider;
    }
}
