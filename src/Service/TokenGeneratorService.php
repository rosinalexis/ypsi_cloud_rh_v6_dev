<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class TokenGeneratorService
{

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';


    public function getRandomSecureToken(int $length = 30): string
    {
        try {
            $token = '';
            $maxNumber = strlen(self::ALPHABET);

            for ($i = 0; $i < $length; $i++) {

                $token .= self::ALPHABET[random_int(0, $maxNumber - 1)];
            }

        } catch (\Exception $e) {

            $this->logger->error("The serveur can not generate confirmation token. \n ".$e->getMessage());
        }

        return $token;
    }
}