<?php

namespace LahthonyOTPAuthBundle\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class WrongOTPException extends AuthenticationException
{
    public function getMessageKey()
    {
        return 'Invalid Code OTP';
    }
}
