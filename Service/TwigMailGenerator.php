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

    public function getMessage($qrcode)
    {
        $template = $this->twig->render('@LahthonyOTPAuth/Default/mail.twig', [
            'qrcode' => $qrcode,
        ]);

        $swiftMessage = new \Swift_Message('2Factor Auth');
        $swiftMessage
            ->setBody($template, 'text/html')
        ;

        return $swiftMessage;
    }
}
