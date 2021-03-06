<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 01/12/2017
 * Time: 15:02.
 */

namespace LahthonyOTPAuthBundle\Tests\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use LahthonyOTPAuthBundle\EventListener\LoginEventListener;
use LahthonyOTPAuthBundle\Exception\WrongOTPException;
use LahthonyOTPAuthBundle\Manager\OTPManager;
use LahthonyOTPAuthBundle\Tests\TestUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
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

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var ObjectManager
     */
    private $manager;

    public function setUp()
    {
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->flashBag = $this->createMock(FlashBagInterface::class);
        $this->manager = $this->createMock(ObjectManager::class);

        $this->otpManager = new OTPManager(30, 'sha1', 6, '', '', $this->flashBag);

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

        $session = $this->createMock(Session::class);
        $session->method('set')
            ->with($this->equalTo(Security::AUTHENTICATION_ERROR), $this->isInstanceOf(WrongOTPException::class));

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

        $loginEventListener = new LoginEventListener(['ROLE_ADMIN'], $this->tokenStorage, $this->otpManager, $this->manager);
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

        $loginEventListener = new LoginEventListener(['ROLE_ADMIN'], $this->tokenStorage, $this->otpManager, $this->manager);
        $loginEventListener->onAuthenticationSuccess($this->event);
    }

    public function testUserAdminRequiredAnOTPIdentification()
    {
        $user = $this->getUser();
        $user
            ->setSecretAuthKey(null)
            ->setRoles(['ROLE_ADMIN'])
        ;

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')
            ->willReturn($user);

        $this->setEvent($token, null);

        $this->manager
            ->expects($this->once())
            ->method('persist')
            ->with($this->identicalTo($user))
        ;

        $loginEventListener = new LoginEventListener(['ROLE_ADMIN'], $this->tokenStorage, $this->otpManager, $this->manager);
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
