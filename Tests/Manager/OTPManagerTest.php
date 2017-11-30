<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 29/11/2017
 * Time: 13:22.
 */

namespace LahthonyOTPAuthBundle\Tests\Manager;

use LahthonyOTPAuthBundle\Manager\OTPManager;
use LahthonyOTPAuthBundle\Model\OTPAuthInterface;
use OTPHP\TOTP;
use PHPUnit\Framework\TestCase;

class OTPManagerTest extends TestCase
{
    /**
     * @var OTPManager
     */
    private $OTPManager;

    protected function setUp()
    {
        $this->OTPManager = new OTPManager(30, 'sha1', 6, '', '');
    }

    public function testGetOTPClient()
    {
        $key = 'TS5DDYHMAK7GXE4PH4P44OZV7HQEAX7HZDUJSQGTALEMAPH26NWZLSMFSH5I2ORD2F5RZAZJ2I6FIDFODOIOKZG7LT4OFHXF53JZMFQ';

        $object = $this->createMock(OTPAuthInterface::class);
        $object->method('getSecretAuthKey')
            ->willReturn($key)
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

    protected function tearDown()
    {
        $this->OTPManager = '';
    }
}
