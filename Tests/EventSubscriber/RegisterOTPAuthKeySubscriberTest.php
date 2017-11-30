<?php

use Doctrine\ORM\Event\LifecycleEventArgs;
use LahthonyOTPAuthBundle\EventListener\RegisterOTPAuthKeySubscriber;
use LahthonyOTPAuthBundle\Manager\OTPManager;
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 29/11/2017
 * Time: 15:29.
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

    protected function setUp()
    {
        $mailer = $this->createMock(Swift_Mailer::class);
        $otpManager = $this->createMock(OTPManager::class);

        $this->RegisterOTPAuthKeySubscriber = new RegisterOTPAuthKeySubscriber($mailer, $otpManager);

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
        $object = $this->createMock(\LahthonyOTPAuthBundle\Model\OTPAuthInterface::class);
        $object
            ->expects($this->once())
            ->method('getOtp2auth')
            ->willReturn(false);

        $this->arg
            ->method('getObject')
            ->willReturn($object);

        $result = $this->RegisterOTPAuthKeySubscriber->prePersist($this->arg);
        $this->assertNull($result);
    }

    protected function tearDown()
    {
        $this->RegisterOTPAuthKeySubscriber = '';
        $this->arg = '';
        $this->mailer = '';
        $this->otpManager = '';
    }
}
