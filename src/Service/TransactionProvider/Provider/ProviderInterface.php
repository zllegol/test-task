<?php

namespace App\Service\TransactionProvider\Provider;

use App\Model\Transaction;

interface ProviderInterface
{
    /**
     * @param array $params
     * @return array|Transaction[]
     */
    public function getTransactions(array $params = []): array;
}
