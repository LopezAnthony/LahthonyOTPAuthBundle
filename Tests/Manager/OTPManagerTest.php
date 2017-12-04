<?php
/**
 * Created by PhpStorm.
 * TestUser: Etudiant
 * Date: 29/11/2017
 * Time: 13:22.
 */

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

    protected function tearDown()
    {
        $this->OTPManager = '';
    }
}
