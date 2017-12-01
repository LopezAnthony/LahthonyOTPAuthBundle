<?php

namespace LahthonyOTPAuthBundle\Tests\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use LahthonyOTPAuthBundle\EventListener\RegisterOTPAuthKeySubscriber;
use LahthonyOTPAuthBundle\Manager\OTPManager;
use LahthonyOTPAuthBundle\Model\OTPAuthInterface;
use LahthonyOTPAuthBundle\Service\TwigMailGenerator;
use LahthonyOTPAuthBundle\Tests\TestUser;
use OTPHP\OTP;
use PHPUnit\Framework\TestCase;
use Swift_Mailer;
use Swift_Message;

/**
 * Created by PhpStorm.
 * TestUser: Etudiant
 * Date: 29/11/2017
 * Time: 15:29
 */

class RegisterOTPAuthKeySubscriberTest extends TestCase
{
    /**
     * @var OTPManager
     */
    private $otpManager;

    /**
     * @var RegisterOTPAuthKeySubscriber
     */
    private $RegisterOTPAuthKeySubscriber;

    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var LifecycleEventArgs
     */
    private $arg;

    /**
     * @var OTP
     */
    private $otp;


    protected function setUp()
    {
        $mailer = $this->createMock(Swift_Mailer::class);
        $otpManager = $this->createMock(OTPManager::class);
        $mailGenerator = $this->createMock(TwigMailGenerator::class);
        $mailGenerator
            ->method('getMessage')
            ->willReturn($this->createMock(Swift_Message::class))
        ;

        $this->RegisterOTPAuthKeySubscriber = new RegisterOTPAuthKeySubscriber('mail@mail.fr', $mailGenerator, $mailer, $otpManager);

        $this->otp = $this->createMock(OTP::class);
        $this->arg = $this->createMock(LifecycleEventArgs::class);
        $this->mailer = $mailer;
        $this->otpManager = $otpManager;
    }

    public function testWillReturnNullIfNotImplementOTPAuthInterface()
    {
        $result = $this->RegisterOTPAuthKeySubscriber->prePersist($this->arg);
        $this->assertNull($result);
    }

    public function testWillReturnNullIf2FactorAuthNotActivated()
    {
        $object = $this->createMock(OTPAuthInterface::class);
        $object
            ->expects($this->once())
            ->method('getOtp2auth')
            ->willReturn(false)
        ;

        $this->arg
            ->method('getObject')
            ->willReturn($object)
        ;

        $result = $this->RegisterOTPAuthKeySubscriber->prePersist($this->arg);
        $this->assertNull($result);
    }

    public function testIfSecretKeyAreGenerateAndAMailSend()
    {
        $key = 'TS5DDYHMAK7GXE4PH4P44OZV7HQEAX7HZDUJSQGTALEMAPH26NWZLSMFSH5I2ORD2F5RZAZJ2I6FIDFODOIOKZG7LT4OFHXF53JZMFQ';

        $object = new TestUser();
        $object->setOTP2Auth(true);

        $this->otpManager
            ->method('generateSecretKey')
            ->willReturn($key)
        ;

        $this->otpManager
            ->method('getOTPClient')
            ->willReturn($this->otp)
        ;

        $this->arg
            ->method('getObject')
            ->willReturn($object)
        ;

        $this->RegisterOTPAuthKeySubscriber->prePersist($this->arg);
        $this->assertEquals($key, $object->getSecretAuthKey());
    }

    protected function tearDown()
    {
        $this->RegisterOTPAuthKeySubscriber = '';
        $this->arg = '';
        $this->mailer = '';
        $this->otpManager = '';
    }
}