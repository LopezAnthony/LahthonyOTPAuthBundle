<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 05/12/2017
 * Time: 10:57
 */

namespace LahthonyOTPAuthBundle\EventListener;


use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use LahthonyOTPAuthBundle\Manager\OTPManager;
use LahthonyOTPAuthBundle\Model\OTPAuthInterface;
use LahthonyOTPAuthBundle\Service\TwigMailGenerator;

class UpdateOTPAuthKeySubscriber implements EventSubscriber
{
    private $mailGenerator;
    private $OTPManager;
    private $mailer;

    public function __construct(TwigMailGenerator $mailGenerator, \Swift_Mailer $mailer, OTPManager $OTPManager)
    {
        $this->mailGenerator = $mailGenerator;
        $this->OTPManager = $OTPManager;
        $this->mailer = $mailer;
    }

    public function preUpdate(LifecycleEventArgs $args)
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
        if (false === $object->getOtp2auth()) {
            return;
        }

        if (true === $object->getSecretAuthKey() && true === $object->getOtp2auth()) {
            //generate secret key register in DB table user
            $object->setSecretAuthKey($this->OTPManager->generateSecretKey());
            //sendmail with qrcode
            $this->sendMessage($object, '2Factor.');
        }
    }

    // TODO: out this class
    public function sendMessage($object, $subject)
    {
        $totp = $this->OTPManager->getOTPClient($object);
        $userEmail = $object->getEmail();
        $message = $this->mailGenerator->getMessage($totp->getQrCodeUri(), 'hello');
        $message->setFrom('mail@mail.fr');
        $message->setTo($userEmail);
        $this->mailer->send($message);
    }

    public function getSubscribedEvents()
    {
        return array(
            'preUpdate',
        );
    }
}