<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 08/12/2017
 * Time: 12:00
 */

namespace LahthonyOTPAuthBundle\Tests\EventListener;


use Doctrine\ORM\Event\LifecycleEventArgs;
use LahthonyOTPAuthBundle\EventListener\UpdateOTPAuthKeySubscriber;
use LahthonyOTPAuthBundle\Manager\OTPManager;
use LahthonyOTPAuthBundle\Model\OTPAuthInterface;
use LahthonyOTPAuthBundle\Tests\TestUser;
use OTPHP\OTP;
use PHPUnit\Framework\TestCase;

class UpdateOTPAuthKeySubscriberTest extends TestCase
{
    /**
     * @var OTPManager
     */
    private $otpManager;

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

        $this->otp = $this->createMock(OTP::class);
        $this->arg = $this->createMock(LifecycleEventArgs::class);

        $this->otpManager = $otpManager;
    }

    public function testWillReturnNullIfNotImplementOTPAuthInterface()
    {
        $UpdateOTPAuthKeySubscriber = new UpdateOTPAuthKeySubscriber($this->otpManager);
        $result = $UpdateOTPAuthKeySubscriber->preUpdate($this->arg);
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

        $UpdateOTPAuthKeySubscriber = new UpdateOTPAuthKeySubscriber($this->otpManager);
        $result = $UpdateOTPAuthKeySubscriber->preUpdate($this->arg);
        $this->assertNull($result);
    }

    public function testIfSecretKeyAreGenerateAndAFlashMessageIsAdded()
    {
        $key = 'TS5DDYHMAK7GXE4PH4P44OZV7HQEAX7HZDUJSQGTALEMAPH26NWZLSMFSH5I2ORD2F5RZAZJ2I6FIDFODOIOKZG7LT4OFHXF53JZMFQ';
        $recoveryKey = [
            'recoveryKey' => 'SFKY6MTXHWAPLK5OHDBCPJF3X7CMWLWX7W6V6SA3KTHMA6FCIMLK4CLFPBYITXKJYRJNUCA4NAA5BVNGZ6CHTIA5JWV75BQF3Q72ODSFDG7WCHCQEJDH2WZLRT2TKCH4GYZBT35JOKYZFNFLA6HVPRDEYPNAVPVCAOLWWBVBI6T4W5LNTUBLMZGA3L2LLTQBLUYYICXNIJ7NI',
            'secret' => 'AI458L2K65',
        ];

        $object = new TestUser();
        $object->setOTP2Auth(true);
        $object->setSecretAuthKey(true);

        $otpManager = $this->otpManager;

        $otpManager
            ->method('generateRecoveryKey')
            ->willReturn($recoveryKey)
        ;

        $otpManager
            ->method('generateSecretKey')
            ->willReturn($key)
        ;

        $otpManager
            ->method('getOTPClient')
            ->willReturn($this->otp)
        ;

        $otpManager
            ->expects($this->once())
            ->method('generateFlash')
            ->with($this->equalTo($recoveryKey['secret']))
        ;

        $this->arg
            ->method('getObject')
            ->willReturn($object)
        ;

        $UpdateOTPAuthKeySubscriber = new UpdateOTPAuthKeySubscriber($otpManager);
        $UpdateOTPAuthKeySubscriber->preUpdate($this->arg);
        $this->assertEquals($key, $object->getSecretAuthKey());
    }

    protected function tearDown()
    {
        $this->arg = '';
        $this->otpManager = '';
    }
}