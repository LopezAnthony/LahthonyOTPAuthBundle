<?php

namespace LahthonyOTPAuthBundle\Model;

/**
 * While implement this interface add a
 * private $secretAuthKey to the TestUser Entity.
 *
 * Interface OTPAuthInterface
 */
interface OTPAuthInterface
{
    /**
     * define if the user accept the 2 factor authentication or not.
     *
     * @param $otp2auth boolean
     *
     * @return bool
     */
    public function setOTP2Auth($otp2auth);

    /**
     * will return bool
     * to accept the registration of the secretAuthKey.
     *
     * @return bool
     */
    public function getOTP2Auth();

    /**
     * @param $secretAuthKey
     *
     * @return mixed
     *               <code>
     *               public function setSecretAuthKey($secretAuthKey)
     *               {
     *               $this->secretAuthKey = $secretAuthKey;
     *               }
     *               </code>
     */
    public function setSecretAuthKey($secretAuthKey);

    /**
     * @return string
     *                <code>
     *                public function getSecretAuthKey()
     *                {
     *                      return $this->secretAuthKey;
     *                }
     *                </code>
     */
    public function getSecretAuthKey();

    /**
     * @param $recoveryKey
     *
     * @return mixed
     *               <code>
     *               public function setRecoveryKey($recoveryKey)
     *               {
     *               $this->recoveryKey = $recoveryKey;
     *               }
     *               </code>
     */
    public function setRecoveryKey($recoveryKey);

    /**
     * @return string
     *                <code>
     *                public function getRecoveryKey()
     *                {
     *                      return return $this->recoveryKey;
     *                }
     *                </code>
     */
    public function getRecovryKey();
    public function getEmail();
}
