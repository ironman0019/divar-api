<?php

namespace App\Http\Services\Otp;

use Exception;

class OtpException extends Exception
{
    public function __construct(string $message, public int $statusCode = 400)
    {
        parent::__construct($message, $statusCode);
    }
}
