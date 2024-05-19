<?php

namespace App\Service\TransactionProvider;

use App\Model\Transaction;
use App\Service\TransactionProvider\Provider\ProviderInterface;

class TransactionProvider
{
    protected ProviderInterface $provider;

    public function __construct(ProviderInterface $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return array|Transaction[]
     */
    public function getTransactions(array $params = []): array
    {
        $res = [];

        foreach ($this->provider->getTransactions($params) as $item) {
            $res[] = new Transaction($item['bin'], $item['amount'], $item['currency']);
        }

        return $res;
    }
}
