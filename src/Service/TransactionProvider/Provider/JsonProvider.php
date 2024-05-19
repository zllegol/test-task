<?php

namespace App\Service\TransactionProvider\Provider;

class JsonProvider implements ProviderInterface
{
    /**
     * @inheritdocs
     */
    public function getTransactions(array $params = []): array
    {
        $res = [];

        $filepath = $params['filepath'] ?? '';

        if (!file_exists($filepath)) {
            throw new \UnexpectedValueException('File does not exist');
        }

        $content = file_get_contents($filepath);
        $content = trim($content);

        $content = explode(PHP_EOL, $content);
        foreach ($content as $line) {
            $line = trim($line);
            if ($line = @json_decode($line, true)) {
                $res[] = $line;
            }
        }

        return array_map(function ($row) {

            $bin = $row['bin'] ?? null;
            $currency = $row['currency'] ?? null;
            $amount = $row['amount'] ?? null;

            $amount = $amount ? floatval($amount) : 0;
            $amount = round($amount, 2);

            return [
                'bin'       => $bin,
                'currency'  => $currency,
                'amount'    => $amount,
            ];

        }, $res);
    }
}
