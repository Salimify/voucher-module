<?php


namespace App\Consumer;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use SymfonyBundles\KafkaBundle\Command\Consumer;

class VoucherConsumer extends Consumer
{
    public const QUEUE_NAME = 'voucher_queue';

    /**
     * {@inheritdoc}
     */
    protected function onMessage(array $data): void
    {
        $command_name = $data[1];
        if ($command_name === "GENERATE_VOUCHER_COMMAND")
        {
                $command = $this->getApplication()->find("app:create-voucher");
                $output = new ConsoleOutput();
                $input = new ArrayInput($data);
                $command->execute($input, $output);
        } else {
            $this->logger->warning("unknown command type");
        }
    }
}