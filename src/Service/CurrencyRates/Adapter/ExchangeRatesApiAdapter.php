<?php

namespace App\Service\CurrencyRates\Adapter;

use App\Model\Transaction;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ExchangeRatesApiAdapter implements CurrencyRatesAdapterInterface
{
    const HTTP_HOST = 'http://api.exchangeratesapi.io/v1/';

    protected string|null $accessKey;

    protected HttpClientInterface|null $client = null;

    public function __construct(array $params = [])
    {
        $this->accessKey = $params['access_key'] ?? null;
        $this->client = $params['client'] ?? HttpClient::createForBaseUri(self::HTTP_HOST);
    }

    public function getExchangeRates(string $currency): array
    {
        $res = [];

        // Support EUR as base currency only, for this adapter, for now!
        if (Transaction::CURRENCY_EUR == $currency) {
            try {
                $response = $this->client->request(
                    'GET',
                    'latest',
                    [
                        'query' => [
                            'access_key' => $this->accessKey,
                        ]
                    ]
                )->toArray();

                if ($response['success'] ?? false) {
                    $res = $response['rates'] ?? [];
                }

            } catch (\Throwable $e) {
                // do nothing
            }
        }

        return $res;
    }
}
