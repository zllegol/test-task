<?php

namespace App\Command;

use App\Context;
use App\Service\TransactionCommission\TransactionCommissionService;
use App\Service\TransactionProvider\TransactionProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CalculateTransactionCommission
 */
class CalculateTransactionCommission extends Command
{
    /** @inheritdocs */
    protected static $defaultName = 'app:calculate-transaction-commission';

    /**
     * @inheritdocs
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('app:calculate-transaction-commission');

        $this->setDescription('Calculate commission for transactions. Transactions will be retrieved from specified source.');

        $this->addArgument('filepath', InputArgument::REQUIRED, 'Filepath');
    }

    /**
     * @inheritdocs
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filepath = $input->getFirstArgument();

        try {
            /** @var TransactionProvider $transactionProvider */
            $transactionProvider = Context::getService(TransactionProvider::class);

            /** @var TransactionCommissionService $transactionCommissionService */
            $transactionCommissionService = Context::getService(TransactionCommissionService::class);

            $transactions = $transactionProvider->getTransactions(['filepath' => $filepath]);

            foreach ($transactions as $transaction) {

                $transactionCommissionService->applyCommission($transaction);

                // Display result
                $output->writeln($transaction->getCommission());
            }

        } catch (\Throwable $e) {
            $output->writeln('<error>Error occurred</error>');
        }

        return 0;
    }
}
