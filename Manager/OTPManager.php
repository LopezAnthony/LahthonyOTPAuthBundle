<?php

namespace LahthonyOTPAuthBundle\Manager;

use LahthonyOTPAuthBundle\Model\OTPAuthInterface;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

class OTPManager
{
    private $period;
    private $digestAlgo;
    private $digit;
    private $issuer;
    private $image;

    public function __construct($period, $digestAlgo, $digit, $issuer, $image)
    {
        $this->period = $period;
        $this->digestAlgo = $digestAlgo;
        $this->digit = $digit;
        $this->issuer = $issuer;
        $this->image = $image;
    }

    public function getOTPClient(OTPAuthInterface $user)
    {
        $totp = TOTP::create(
            $user->getSecretAuthKey(),
            $this->period,
            $this->digestAlgo,
            $this->digit
        );

        (method_exists($user, 'getEmail')) ? $totp->setLabel($user->getEmail()) : $totp->setLabel($user->getUsername());

        $totp->setIssuer($this->issuer);
        $totp->setParameter('image', $this->image);

        return $totp;
    }

    public function generateSecretKey()
    {
        $mySecret = trim(Base32::encodeUpper(random_bytes(128)), '=');
        return $mySecret;
    }
}