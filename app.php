<?php

use App\Command\CalculateTransactionCommission;
use Symfony\Component\Console\Application;

require_once 'vendor/autoload.php';

$application = new Application();

$calculateTransactionCommand = new CalculateTransactionCommission();

$application->add($calculateTransactionCommand);
$application->setDefaultCommand($calculateTransactionCommand->getName(), true);

$application->run();
