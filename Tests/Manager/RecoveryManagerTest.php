<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 08/12/2017
 * Time: 11:32.
 */

namespace LahthonyOTPAuthBundle\Tests\Manager;

use LahthonyOTPAuthBundle\Manager\RecoveryManager;
use LahthonyOTPAuthBundle\Tests\TestUser;
use PHPUnit\Framework\TestCase;

class RecoveryManagerTest extends TestCase
{
    /**
     * @dataProvider
     */
    public function testSubmitSecretCode()
    {
        $secret = '6L6H2B74ZM';
        $secretAuthKey = '5RHMZHERSOWCQZ7TNX7Z2FHJOCCFSEHR6NQMQ2GMPB4PMROOQQ3BI2LPURNLGIZTYQMTFLSZ4WARICDJ2RH5R645EVBIHIJND4WYZQX45KU77HO4PFI7Y3UUED2ZHIYIUBCQ2HBMHYX6XYQ26JVT5SKYQQ5HFUERFCIOWICAAXMUREXIMCLJD23BSMKXMXGHHVQGYKXY54RBQ';
        $recoverykey = 'e2de5775bd76d6a9d6b0c17a8ea66a6b6dc13953';

        $user = $this->createMock(TestUser::class);
        $user
            ->method('getSecretAuthKey')
            ->willReturn($secretAuthKey)
        ;
        $user
            ->method('getRecoveryKey')
            ->willReturn($recoverykey)
        ;
        $user
            ->method('getEmail')
            ->willReturn('toto@gmail.com')
        ;

        $recovery = new RecoveryManager();
        $result = $recovery->verifyOTPKey($secret, $user);

        $this->assertTrue($result);
    }

    /**
     * @dataProvider badSecretCodeProvider
     */
    public function testInvalidSecretCode($code)
    {
        $secret = $code;
        $secretAuthKey = '5RHMZHERSOWCQZ7TNX7Z2FHJOCCFSEHR6NQMQ2GMPB4PMROOQQ3BI2LPURNLGIZTYQMTFLSZ4WARICDJ2RH5R645EVBIHIJND4WYZQX45KU77HO4PFI7Y3UUED2ZHIYIUBCQ2HBMHYX6XYQ26JVT5SKYQQ5HFUERFCIOWICAAXMUREXIMCLJD23BSMKXMXGHHVQGYKXY54RBQ';
        $recoverykey = 'e2de5775bd76d6a9d6b0c17a8ea66a6b6dc13953';

        $user = $this->createMock(TestUser::class);
        $user
            ->method('getSecretAuthKey')
            ->willReturn($secretAuthKey)
        ;
        $user
            ->method('getRecoveryKey')
            ->willReturn($recoverykey)
        ;
        $user
            ->method('getEmail')
            ->willReturn('toto@gmail.com')
        ;

        $recovery = new RecoveryManager();
        $result = $recovery->verifyOTPKey($secret, $user);

        $this->assertFalse($result);
    }

    public function testResetOTPIdentificationOfUser()
    {
        $user = $this->createMock(TestUser::class);
        $user
            ->expects($this->once())
            ->method('setSecretAuthKey')
            ->with(null)
        ;
        $user
            ->expects($this->once())
            ->method('setRecoveryKey')
            ->with(null)
        ;

        $recovery = new RecoveryManager();
        $recovery->resetOTP($user);
    }

    public function badSecretCodeProvider()
    {
        return [
            ['6L662r78ZM'],
            ['56544343543434343'],
            ['lhfhjqsdlqmsdlqdqsdk'],
        ];
    }
}
