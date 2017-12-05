<?php

namespace LahthonyOTPAuthBundle\Service;

use Twig_Environment;

class TwigMailGenerator
{
    protected $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getMessage($qrcode, $otpkey)
    {
        $template = $this->twig->render('@LahthonyOTPAuth/Default/mail.twig', [
            'qrcode' => $qrcode,
            'otpkey' => $otpkey,
        ]);

        $swiftMessage = new \Swift_Message('2Factor Auth');
        $swiftMessage
            ->setBody($template, 'text/html')
        ;

        return $swiftMessage;
    }
}
