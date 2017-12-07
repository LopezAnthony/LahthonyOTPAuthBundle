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

    public function getMessage($email)
    {
        $template = $this->twig->render('@LahthonyOTPAuth/mail.twig', [
            'email' => $email,
        ]);

        $swiftMessage = new \Swift_Message('2Factor Auth');
        $swiftMessage
            ->setBody($template, 'text/html')
        ;

        return $swiftMessage;
    }
}
