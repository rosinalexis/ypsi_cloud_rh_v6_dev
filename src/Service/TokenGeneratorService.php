<?php

namespace App\Service;

use Symfony\Component\CssSelector\Exception\InternalErrorException;

class TokenGeneratorService
{

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
            // TODO : ADD manage exception
        }

        return $token;
    }
}