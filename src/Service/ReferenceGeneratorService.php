<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Ulid;

class ReferenceGeneratorService
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getRandomSecureReference():string
    {
        $this->logger->debug("Generate reference from Ui");

        $uuid = new Ulid();

        return "ref-".date("Y")."-".$uuid->toRfc4122();
    }

}