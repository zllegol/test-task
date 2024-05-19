<?php

namespace App\Service\BinCountryDetector\Adapter;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinlistAdapter implements BinCountryDetectorAdapterInterface
{
    const HTTP_HOST = 'https://lookup.binlist.net';

    protected HttpClientInterface|null $client = null;

    public function __construct(array $params = [])
    {
        $this->client = $params['client'] ?? HttpClient::create();
    }

    public function detectCountry(string $bin): array
    {
        $res = [];

        try {
            $response = $this->client->request('GET', self::HTTP_HOST . '/' . $bin)->toArray();

            if ($response && array_key_exists('country', $response)) {
                $country = $response['country'];

                $res = [
                    'name' => $country['name'] ?? null,
                    'alpha2' => $country['alpha2'] ?? null,
                    'numeric' => $country['numeric'] ?? null,
                ];

            }
        } catch (\Throwable $e) {
            // do nothing
        }

        return $res;
    }
}
