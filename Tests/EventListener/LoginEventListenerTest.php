<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 01/12/2017
 * Time: 15:02.
 */

namespace LahthonyOTPAuthBundle\Tests\EventListener;

use LahthonyOTPAuthBundle\EventListener\LoginEventListener;
use LahthonyOTPAuthBundle\Exception\WrongOTPException;
use LahthonyOTPAuthBundle\Manager\OTPManager;
use LahthonyOTPAuthBundle\Tests\TestUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginEventListenerTest extends TestCase
{
    /**
     * @var
     */
    private $tokenStorage;
    /**
     * @var OTPManager
     */
    private $otpManager;

    /**
     * @var
     */
    private $event;

    public function setUp()
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);

        $this->otpManager = new OTPManager(30, 'sha1', 6, '', '');

        $this->event = $this->getMockBuilder(InteractiveLoginEvent::class)
            ->setMethods(['getAuthenticationToken', 'getRequest'])
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function testWithBadCode()
    {
        $user = $this->getUser();

        $this->tokenStorage
            ->expects($this->once())
            ->method('setToken')
        ;

        $session = $this->createMock(SessionInterface::class);
        $session
            ->expects($this->once())
            ->method('set')
            ->with(
                $this->equalTo(Security::AUTHENTICATION_ERROR),
                $this->equalTo(new WrongOTPException())
            );

        $request = $this->createMock(Request::class);
        $request
            ->method('get')
            ->with('otp')
            ->willReturn(124567);

        $request
            ->method('getSession')
            ->willReturn($session);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')
            ->willReturn($user);

        $this->setEvent($token, $request);

        $loginEventListener = new LoginEventListener($this->tokenStorage, $this->otpManager);
        $loginEventListener->onAuthenticationSuccess($this->event);
    }

    public function testSuccessFullAuthentication()
    {
        $user = $this->getUser();

        $this->tokenStorage
            ->expects($this->never())
            ->method('setToken')
        ;

        $otpclient = $this->otpManager->getOTPClient($user);

        $request = $this->createMock(Request::class);
        $request
            ->method('get')
            ->with('otp')
            ->willReturn($otpclient->now());

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')
            ->willReturn($user);

        $this->setEvent($token, $request);

        $loginEventListener = new LoginEventListener($this->tokenStorage, $this->otpManager);
        $loginEventListener->onAuthenticationSuccess($this->event);
    }

    private function setEvent($token, $request)
    {
        $this->event
            ->method('getAuthenticationToken')
            ->willReturn($token)
        ;

        $this->event
            ->method('getRequest')
            ->willReturn($request)
        ;
    }

    private function getUser()
    {
        $key = 'TS5DDYHMAK7GXE4PH4P44OZV7HQEAX7HZDUJSQGTALEMAPH26NWZLSMFSH5I2ORD2F5RZAZJ2I6FIDFODOIOKZG7LT4OFHXF53JZMFQ';

        $user = new TestUser();

        $user
            ->setEmail('mail@mail.fr')
            ->setSecretAuthKey($key)
        ;

        return $user;
    }
}
