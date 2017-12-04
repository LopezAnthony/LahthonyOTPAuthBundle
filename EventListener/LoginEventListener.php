<?php
/**
 * Created by PhpStorm.
 * TestUser: Etudiant
 * Date: 30/11/2017
 * Time: 15:24.
 */

namespace LahthonyOTPAuthBundle\EventListener;

use LahthonyOTPAuthBundle\Manager\OTPManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginEventListener implements EventSubscriberInterface
{
    private $tokenStorage;

    /**
     * @var OTPManager
     */
    private $OTPManager;

    public function __construct(TokenStorageInterface $tokenStorage, OTPManager $OTPManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->OTPManager = $OTPManager;
    }

    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();

        if (null !== $user->getSecretAuthKey()) {
            $totp = $this->OTPManager->getOTPClient($user);
            $otp = $event->getRequest()->get('otp');
            if (false === $totp->verify($otp)) {
                $this->tokenStorage->setToken(null);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array('onAuthenticationSuccess');
    }
}
