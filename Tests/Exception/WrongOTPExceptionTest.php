<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 08/12/2017
 * Time: 11:55.
 */

namespace LahthonyOTPAuthBundle\Tests\Exception;

use LahthonyOTPAuthBundle\Exception\WrongOTPException;
use PHPUnit\Framework\TestCase;

class WrongOTPExceptionTest extends TestCase
{
    public function testGetMessage()
    {
        $exception = new WrongOTPException();
        $this->assertEquals('Invalid Code OTP', $exception->getMessageKey());
    }
}
