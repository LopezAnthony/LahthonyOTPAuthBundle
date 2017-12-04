<?php

namespace LahthonyOTPAuthBundle\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 04/12/2017
 * Time: 16:50
 */

class WrongOTPException extends AuthenticationException
{
    public function getMessageKey()
    {
        return 'Invalid Code OTP';
    }
}