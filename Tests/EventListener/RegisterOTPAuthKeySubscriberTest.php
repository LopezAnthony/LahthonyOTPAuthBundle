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
        $otpManager = $this->createMock(OTPManager::class);

        $this->RegisterOTPAuthKeySubscriber = new RegisterOTPAuthKeySubscriber($otpManager);

        $this->otp = $this->createMock(OTP::class);
        $this->arg = $this->createMock(LifecycleEventArgs::class);

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

    public function testIfSecretKeyAreGenerateAndAFlashMessageIsAdded()
    {
        $key = 'TS5DDYHMAK7GXE4PH4P44OZV7HQEAX7HZDUJSQGTALEMAPH26NWZLSMFSH5I2ORD2F5RZAZJ2I6FIDFODOIOKZG7LT4OFHXF53JZMFQ';
        $recoveryKey = [
            'recoveryKey' => 'SFKY6MTXHWAPLK5OHDBCPJF3X7CMWLWX7W6V6SA3KTHMA6FCIMLK4CLFPBYITXKJYRJNUCA4NAA5BVNGZ6CHTIA5JWV75BQF3Q72ODSFDG7WCHCQEJDH2WZLRT2TKCH4GYZBT35JOKYZFNFLA6HVPRDEYPNAVPVCAOLWWBVBI6T4W5LNTUBLMZGA3L2LLTQBLUYYICXNIJ7NI',
            'secret' => 'AI458L2K65'
        ];

        $object = new TestUser();
        $object->setOTP2Auth(true);

        $this->otpManager
            ->method('generateRecoveryKey')
            ->willReturn($recoveryKey)
        ;

        $this->otpManager
            ->method('generateSecretKey')
            ->willReturn($key)
        ;

        $this->otpManager
            ->method('getOTPClient')
            ->willReturn($this->otp)
        ;

        $this->otpManager
            ->expects($this->once())
            ->method('generateFlash')
            ->with($this->equalTo($recoveryKey['secret']))
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
