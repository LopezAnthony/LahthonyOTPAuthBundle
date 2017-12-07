<?php

namespace LahthonyOTPAuthBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use LahthonyOTPAuthBundle\Exception\WrongOTPException;
use LahthonyOTPAuthBundle\Manager\OTPManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginEventListener
{
    private $tokenStorage;
    private $OTPManager;
    private $roles;
    private $manager;

    public function __construct($roles, TokenStorageInterface $tokenStorage, OTPManager $OTPManager, ObjectManager $manager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->OTPManager = $OTPManager;
        $this->roles = $roles;
        $this->manager = $manager;
    }

    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();
        if (null !== $user->getSecretAuthKey()) {
            $totp = $this->OTPManager->getOTPClient($user);
            $otp = $event->getRequest()->get('otp');
            if (false === $totp->verify($otp)) {
                $event->getRequest()->getSession()->set(Security::AUTHENTICATION_ERROR, new WrongOTPException());
                $this->tokenStorage->setToken(null);
            }
        }
        if (null === $user->getSecretAuthKey()) {
            foreach ($user->getRoles() as $role) {
                if (in_array($role, $this->roles) && null === $user->getSecretAuthKey()) {
                    $user->setSecretAuthKey($this->OTPManager->generateSecretKey());
                    $recovery = $this->OTPManager->generateRecoveryKey($user);
                    $user->setRecoveryKey($recovery['recoveryKey']);
                    $this->manager->persist($user);
                    $this->manager->flush();
                    $totp = $this->OTPManager->getOTPClient($user);
                    $this->OTPManager->generateFlash($recovery['secret'], $totp->getQrCodeUri());
                }
            }
        }
    }
}
