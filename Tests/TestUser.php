<?php

namespace LahthonyOTPAuthBundle\Tests;

use Doctrine\ORM\Mapping as ORM;
use LahthonyOTPAuthBundle\Model\OTPAuthInterface;

//use Symfony\Component\Security\Core\User\UserInterface;

/**
 * TestUser.
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class TestUser implements OTPAuthInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(name="secret_auth_key", type="string", length=255, nullable=true)
     */
    private $secretAuthKey;

    /**
     * @var string
     * @ORM\Column(name="secret_auth_key", type="string", length=255, nullable=true)
     */
    private $recoveryKey;

    /**
     * @var bool
     */
    private $OTP2Auth;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return TestUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return TestUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return TestUser
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * @return string|null The salt
     */
    public function getSalt()
    {
        return false;
    }

    public function eraseCredentials()
    {
    }

    public function setOTP2Auth($otp2auth)
    {
        $this->OTP2Auth = $otp2auth;
    }

    public function getOTP2Auth()
    {
        return $this->OTP2Auth;
    }

    public function setSecretAuthKey($secretAuthKey)
    {
        $this->secretAuthKey = $secretAuthKey;
    }

    public function getSecretAuthKey()
    {
        return $this->secretAuthKey;
    }

    /**
     * @return mixed
     */
    public function getRecoveryKey()
    {
        return $this->recoveryKey;
    }

    /**
     * @param mixed $recoveryKey
     *
     * @return string
     */
    public function setRecoveryKey($recoveryKey)
    {
        $this->recoveryKey = $recoveryKey;
    }
}
