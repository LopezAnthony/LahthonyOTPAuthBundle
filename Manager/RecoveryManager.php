<?php

namespace LahthonyOTPAuthBundle\Manager;

use LahthonyOTPAuthBundle\Model\OTPAuthInterface;

class RecoveryManager
{
    public function verifyOTPKey($secret, OTPAuthInterface $user)
    {
        $email = $user->getEmail();
        $secretAuthKey = $user->getSecretAuthKey();

        if (true !== hash_equals((hash_hmac('ripemd160', $email.$secretAuthKey, $secret)), $user->getRecoveryKey())) {
            return false;
        }

        return true;
    }

    public function resetOTP(OTPAuthInterface $user)
    {
        $user->setSecretAuthKey(null);
        $user->setRecoveryKey(null);
    }
}
