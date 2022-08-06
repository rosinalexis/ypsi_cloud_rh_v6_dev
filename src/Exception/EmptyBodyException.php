<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class EmptyBodyException extends HttpException
{
   public function __construct(
       int $statusCode=400,
       string $message = 'The body of the POST/PUT method cannot be empty.',
       Throwable $previous = null,
       array $headers = [],
       int $code = 0)
   {
       parent::__construct($statusCode, $message, $previous, $headers, $code);
   }
}