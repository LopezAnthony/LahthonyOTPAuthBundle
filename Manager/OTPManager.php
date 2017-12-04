<?php

namespace LahthonyOTPAuthBundle\Manager;

use LahthonyOTPAuthBundle\Model\OTPAuthInterface;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

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
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct($period, $digestAlgo, $digit, $issuer, $image, FlashBagInterface $flashBag)
    {
        // TODO: use option resolver?
        $this->period = $period;
        $this->digestAlgo = $digestAlgo;
        $this->digit = $digit;
        $this->issuer = $issuer;
        $this->image = $image;
        $this->flashBag = $flashBag;
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

    public function generateRecoveryKey(OTPAuthInterface $user)
    {
        $secret = trim(Base32::encodeUpper(random_bytes(6)), '=');
        $recoveryKey = hash_hmac('ripemd160', $user->getEmail().$user->getSecretAuthKey(), $secret);

        return array(
            'secret' => $secret,
            'recoveryKey' => $recoveryKey,
        );
    }

    public function generateFlash($recoveryKey, $authKey, $qrCodeUri)
    {
        return $this->flashBag->add('2factor',
            "<div>
                <p>To use your 2factor authenticator you'll need google authenticator or any app like so.</p>
                <p>You can download it <a href=\"https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=fr\">Here</a> for playstore.</p> 
                <p>Then all you need is to scan the following qrcode or enter the authentication key in your app.</p>
                <p>Secret Authentication Key : $authKey </p>
                <p>QRCode: <img src=\"$qrCodeUri\"></p>
                <p>Please make sure to write down the following code (you'll need it if you lose your device) : <strong> $recoveryKey </strong></p>
            </div>"
        );
    }
}
