<?php

namespace Service\BinCountryDetector;

use App\Service\BinCountryDetector\Adapter\BinlistAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\JsonMockResponse;

class BinlistAdapterTest extends TestCase
{
    protected $binMap = [];

    protected $county;

    protected function setUp(): void
    {
        $this->binMap = [
            '45717360' => '{"number":{},"scheme":"visa","type":"debit","brand":"Visa Classic","country":{"numeric":"208","alpha2":"DK","name":"Denmark","emoji":"🇩🇰","currency":"DKK","latitude":56,"longitude":10},"bank":{"name":"Jyske Bank A/S"}}',
        ];

        $this->county = [
            'name' => 'Denmark',
            'alpha2' => 'DK',
            'numeric' => '208'
        ];
    }

    public function testSuccessResponse()
    {
        $bin = '45717360';

        $adapter = $this->getAdapter();
//
        $res = $adapter->detectCountry($bin);
//
        $this->assertEquals($this->county, $res);
    }

    public function testFailResponse()
    {
        $adapter = $this->getAdapter();
//
        $res = $adapter->detectCountry('');
//
        $this->assertEquals([], $res);
    }

    protected function getAdapter(): BinlistAdapter
    {
        $client = new MockHttpClient(function ($m, $u, $o) {
            $u = strtr($u, [BinlistAdapter::HTTP_HOST . '/' => '']);

            $content = json_decode($this->binMap[$u] ?? '', true);

            $response = new JsonMockResponse($content);
            return $response;
        });

        $adapter = new BinlistAdapter(['client' => $client]);

        return $adapter;
    }
}
