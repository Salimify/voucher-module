<?php


namespace App\Command;

use App\Entity\Voucher;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateVoucherCommand extends Command
{
    protected static $defaultName = 'app:create-voucher';

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $order_id = $input->getFirstArgument();
        $entityManager = $this->entityManager;
        $entityManager->getConnection()->beginTransaction();
        try {
            $voucher = new Voucher();
            $voucher->setCode($this->generateRandomString(10));
            $voucher->setOrderId($order_id);
            $entityManager->persist($voucher);
            $entityManager->flush();
            $entityManager->getConnection()->commit();
            $output->writeln("Voucher created successfully for order ".$order_id);
            return 0;
        } catch (Exception $e) {
            $entityManager->getConnection()->rollBack();
            $output->writeln("Voucher creation failed for order ".$order_id);
            return 1;
        }
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}