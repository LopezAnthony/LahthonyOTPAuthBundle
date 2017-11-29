<?php

namespace LahthonyOTPAuthBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use LahthonyOTPAuthBundle\Manager\OTPManager;
use LahthonyOTPAuthBundle\Model\OTPAuthInterface;

class RegisterOTPAuthKeySubscriber implements EventSubscriber
{

    private $mailer;
    private $OTPManager;

    public function __construct(\Swift_Mailer $mailer, OTPManager $OTPManager)
    {
        $this->mailer = $mailer;
        $this->OTPManager = $OTPManager;
    }

    public function prePersist(LifecycleEventArgs $args)
    {

        $object = $args->getObject();


        /**
         * will execute only if instance of OTPAuthInterface
         */
        if (!$object instanceof OTPAuthInterface) {
            return;
        }


        /**
         * If the user doesn't accept the 2 factor authentication
         * stop execution
         */
        if (false == $object->getOtp2auth()) {
            return;
        }


        if (null === $object->getSecretAuthKey()) {
            #generate secret key register in DB table user
            $object->setSecretAuthKey($this->OTPManager->generateSecretKey());
            #sendmail with qrcode
            $this->sendMessage($object, '2Factor.');
        }
    }

    public function sendMessage($object, $subject)
    {
        $totp = $this->OTPManager->getOTPClient($object);

        $userEmail = $object->getEmail();
        $message = (new \Swift_Message($subject))
            ->setFrom('admin@gmail.com')
            ->setTo($userEmail)
            ->setBody('<img src="' . $totp->getQrCodeUri(). '">', 'text/html')
        ;

        $this->mailer->send($message);

    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist'
        );
    }
}