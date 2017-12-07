<?php

namespace LahthonyOTPAuthBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use LahthonyOTPAuthBundle\Manager\OTPManager;
use LahthonyOTPAuthBundle\Model\OTPAuthInterface;
use LahthonyOTPAuthBundle\Service\TwigMailGenerator;

class RegisterOTPAuthKeySubscriber implements EventSubscriber
{
    private $mailGenerator;
    private $OTPManager;
    private $mailer;
    private $sender;

    public function __construct($sender, TwigMailGenerator $mailGenerator, \Swift_Mailer $mailer, OTPManager $OTPManager)
    {
        $this->mailGenerator = $mailGenerator;
        $this->OTPManager = $OTPManager;
        $this->mailer = $mailer;
        $this->sender = $sender;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();

        /*
         * will execute only if instance of OTPAuthInterface
         */
        if (!$object instanceof OTPAuthInterface) {
            return;
        }

        /*
         * If the user doesn't accept the 2 factor authentication
         * stop execution
         */
        if (false == $object->getOtp2auth()) {
            return;
        }

        if (null === $object->getSecretAuthKey()) {
            //generate secret key register in DB table user
            $authKey =  $this->OTPManager->generateSecretKey();
            $object->setSecretAuthKey($authKey);

            //generate recovery key + secret pass for user
            $recoveryKey = $this->OTPManager->generateRecoveryKey($object);
            $object->setRecoveryKey($recoveryKey["recoveryKey"]);

            //Get the QRCode to display in the flash message
            $totp = $this->OTPManager->getOTPClient($object);
            $QRCode = $totp->getQrCodeUri();

            $this->OTPManager->generateFlash($recoveryKey['secret'], $QRCode);

        }
    }

//TODO may be removed?
//    public function sendMessage($object, $subject)
//    {
//        $totp = $this->OTPManager->getOTPClient($object);
//        $userEmail = $object->getEmail();
//        $message = $this->mailGenerator->getMessage($totp->getQrCodeUri());
//        $message->setFrom($this->sender);
//        $message->setTo($userEmail);
//        $this->mailer->send($message);
//    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
        );
    }
}
