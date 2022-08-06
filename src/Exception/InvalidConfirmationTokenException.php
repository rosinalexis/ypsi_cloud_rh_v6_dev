<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class InvalidConfirmationTokenException extends HttpException
{

   public function __construct(string $message = "Confirmation token is invalid", int $code = 404, ?Throwable $previous = null)
   {
       parent::__construct($message, $code, $previous);
   }
}