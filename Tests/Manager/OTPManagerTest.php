<?php

namespace LahthonyOTPAuthBundle\Tests\Manager;

use LahthonyOTPAuthBundle\Manager\OTPManager;
use LahthonyOTPAuthBundle\Model\OTPAuthInterface;
use OTPHP\TOTP;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class OTPManagerTest extends TestCase
{
    /**
     * @var OTPManager
     */
    private $OTPManager;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    protected function setUp()
    {
        $this->flashBag = $this->createMock(FlashBagInterface::class);
        $this->OTPManager = new OTPManager(30, 'sha1', 6, '', '', $this->flashBag);
    }

    public function testGetOTPClient()
    {
        $key = 'TS5DDYHMAK7GXE4PH4P44OZV7HQEAX7HZDUJSQGTALEMAPH26NWZLSMFSH5I2ORD2F5RZAZJ2I6FIDFODOIOKZG7LT4OFHXF53JZMFQ';

        $object = $this->createMock(OTPAuthInterface::class);
        $object->method('getSecretAuthKey')
            ->willReturn($key)
        ;

        $object->method('getEmail')
            ->willReturn('mail@mail.fr')
        ;

        $otpClient = $this->OTPManager->getOTPClient($object);
        $this->assertInstanceOf(TOTP::class, $otpClient);
        $this->assertEquals($key, $otpClient->getSecret());
    }

    public function testReturnNullIfUserDoNotHaveASecretKey()
    {
        $object = $this->createMock(OTPAuthInterface::class);

        $otpClient = $this->OTPManager->getOTPClient($object);
        $this->assertNull($otpClient);
    }

    public function testGenerateSecretKey()
    {
        $secretKey = $this->OTPManager->generateSecretKey();

        $this->assertInternalType('string', $secretKey);
        $this->assertGreaterThanOrEqual(60, mb_strlen($secretKey));
    }

    public function testGenerateRecoveryKey()
    {
        $key = 'TS5DDYHMAK7GXE4PH4P44OZV7HQEAX7HZDUJSQGTALEMAPH26NWZLSMFSH5I2ORD2F5RZAZJ2I6FIDFODOIOKZG7LT4OFHXF53JZMFQ';

        $object = $this->createMock(OTPAuthInterface::class);
        $object->method('getSecretAuthKey')
            ->willReturn($key)
        ;

        $object->method('getEmail')
            ->willReturn('mail@mail.fr')
        ;

        $recoveryKey = $this->OTPManager->generateRecoveryKey($object);

        $this->assertArrayHasKey('secret', $recoveryKey);
        $this->assertArrayHasKey('recoveryKey', $recoveryKey);
        $this->assertInternalType('string', $recoveryKey['recoveryKey']);
        $this->assertGreaterThanOrEqual(10, mb_strlen($recoveryKey['secret']));
    }

    public function testAddMessageFlash()
    {
        $secret = 'AHD54F5PO3';
        $qrCode = 'https://camo.githubusercontent.com/709c5e44fdc6499404e36c8bc3a41b985b44e593/687474703a2f2f63686172742e617069732e676f6f676c652e636f6d2f63686172743f6368743d7172266368733d323530783235302663686c3d6f747061757468253341253246253246746f74702532464d7925323532304269672532353230436f6d70616e79253341616c6963652532353430676f6f676c652e636f6d2533467365637265742533444a425357593344504548504b335058502532366973737565722533444d7925323532304269672532353230436f6d70616e79';

        $this->flashBag
            ->expects($this->once())
            ->method('add')
            ->with($this->equalTo('2factor'), $this->stringContains($secret))
        ;

        $this->OTPManager->generateFlash($secret, $qrCode);
    }

    protected function tearDown()
    {
        $this->OTPManager = '';
    }
}
