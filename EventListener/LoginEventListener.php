<?php
/**
 * Created by PhpStorm.
 * TestUser: Etudiant
 * Date: 30/11/2017
 * Time: 15:24.
 */

namespace LahthonyOTPAuthBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use LahthonyOTPAuthBundle\Exception\WrongOTPException;
use LahthonyOTPAuthBundle\Manager\OTPManager;
use LahthonyOTPAuthBundle\Service\TwigMailGenerator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginEventListener
{
    private $tokenStorage;

    /**
     * @var OTPManager
     */
    private $OTPManager;
    /**
     * @var
     */
    private $roles;
    /**
     * @var TwigMailGenerator
     */
    private $mailGenerator;
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct($roles, TokenStorageInterface $tokenStorage, OTPManager $OTPManager, TwigMailGenerator $mailGenerator, \Swift_Mailer $mailer, ObjectManager $manager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->OTPManager = $OTPManager;
        $this->roles = $roles;
        $this->mailGenerator = $mailGenerator;
        $this->mailer = $mailer;
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

        if(null === $user->getSecretAuthKey())
        {
            foreach ($user->getRoles() as $role)
            {
                if(in_array($role, $this->roles) && null === $user->getSecretAuthKey())
                {
                    $user->setSecretAuthKey($this->OTPManager->generateSecretKey());
                    $this->manager->persist($user);
                    $this->manager->flush();
                    $this->sendMessage($user);
                }
            }
        }
    }

    public function sendMessage($object)
    {
        $totp = $this->OTPManager->getOTPClient($object);
        $userEmail = $object->getEmail();
        $message = $this->mailGenerator->getMessage($totp->getQrCodeUri(), 'code');
        $message->setFrom('mail@mail.fr');
        $message->setTo($userEmail);
        $this->mailer->send($message);
    }

}
