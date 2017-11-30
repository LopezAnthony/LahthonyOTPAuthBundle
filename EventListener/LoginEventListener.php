<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 30/11/2017
 * Time: 15:24
 */

namespace LahthonyOTPAuthBundle\EventListener;


use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use OTPHP\TOTP;

class LoginEventListener implements EventSubscriberInterface
{

    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();


        if (null !== $user->getSecretAuthKey()){
            $secret = $user->getSecretAuthKey();
            $totp = TOTP::create($secret);
            $otp = $event->getRequest()->get('otp');
            if(false === $totp->verify($otp)){
                 $this->tokenStorage->setToken(null);
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return array('onAuthenticationSuccess');
    }
}