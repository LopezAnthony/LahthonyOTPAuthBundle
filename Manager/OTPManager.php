<?php

namespace LahthonyOTPAuthBundle\Manager;

use LahthonyOTPAuthBundle\Model\OTPAuthInterface;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

/**
 * Class OTPManager.
 */
class OTPManager
{
    /**
     * @var int
     */
    private $period;

    /**
     * @var string
     */
    private $digestAlgo;

    /**
     * @var int
     */
    private $digit;

    /**
     * @var string
     */
    private $issuer;

    /**
     * @var string
     */
    private $image;

    public function __construct($period, $digestAlgo, $digit, $issuer, $image)
    {
        // TODO: use option resolver?
        $this->period = $period;
        $this->digestAlgo = $digestAlgo;
        $this->digit = $digit;
        $this->issuer = $issuer;
        $this->image = $image;
    }

    /**
     * Initialize and return a instance of TOTP.
     *
     * @param OTPAuthInterface $user
     *
     * @return TOTP|void
     */
    public function getOTPClient(OTPAuthInterface $user)
    {
        if (null === $user->getSecretAuthKey()) {
            return;
        }

        $totp = TOTP::create(
            $user->getSecretAuthKey(),
            $this->period,
            $this->digestAlgo,
            $this->digit
        );

        (method_exists($user, 'getEmail')) ? $totp->setLabel($user->getEmail()) : $totp->setLabel('');

        $totp->setIssuer($this->issuer);
        $totp->setParameter('image', $this->image);

        return $totp;
    }

    /**
     * generate secret key base 32.
     *
     * @return string
     */
    public function generateSecretKey()
    {
        $mySecret = trim(Base32::encodeUpper(random_bytes(128)), '=');

        return $mySecret;
    }
}
