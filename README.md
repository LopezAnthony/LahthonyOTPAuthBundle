LahthonyOTPAuthBundle
============

[![Build Status](https://travis-ci.org/LopezAnthony/LahthonyOTPAuthBundle.svg?branch=featureCI)](https://travis-ci.org/LopezAnthony/LahthonyOTPAuthBundle)
[![StyleCI](https://styleci.io/repos/112461062/shield?branch=featureCI)](https://styleci.io/repos/112461062)
[![Coverage Status](https://coveralls.io/repos/github/LopezAnthony/LahthonyOTPAuthBundle/badge.svg?branch=master)](https://coveralls.io/github/LopezAnthony/LahthonyOTPAuthBundle?branch=master)

About :
--------------------------

This bundle permits to easy implements *2 factor authentication* in a symfony project. 

**Users** will then get **TOTP** authentication by using apps like `Google Authenticator`

Let's get started. Just got through the following steps. And it will work.

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require lahthony/otp-auth-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new LahthonyOTPAuthBundle\LahthonyOTPAuthBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Implements OTPAuthInterface
-------------------------

You need to implement the OTPAuthInterface on your User Entity commonly present in `src/AppBundle/Entity/User`. 

:warning: **Do not forget to generate getter setter.** :warning:

```php
<?php
UserTest

use LahthonyOTPAuthBundle\Model\OTPAuthInterface;
//...

class User implements OTPAuthInterface
{
    /**
     * This attribute need to stock in Database 
     * @ORM\Column(name="secret_auth_key", type="string", length=255, nullable=true)
     */
    private $secretAuthKey;

    /**
     * This attribute will permits to do verification on user registration 
     * if he accepts 2Factor Authentication 
     * @var boolean
     */
    private $OTP2Auth;
  
    /**
     * DO NOT FORGET TO GENERATE GETTER AND SETTER FOR THESE TWO ATTRIBUTES 
     */
    
    //We'll need an email to send a qrcode to users
    public function getEmail(){}
        
}
```
:warning: **After that DO NOT FORGET to schema update:** :warning:

```console
$ php bin/console doctrine:schema:update --force
```


Step 4: Add on field to your `UserFormType`
-------------------------

We've made for you an eventsubscriber that permits you to add the required field easily on your`UserFormType`

Just do like so:

```php
<?php
//src/AppBundle/Form/UserType

use LahthonyOTPAuthBundle\EventListener\Add2FactorAuthFieldListener;
//...

class UserType 
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //...
            ->addEventSubscriber(new Add2FactorAuthFieldListener())
        ;
    }
    //...
```
Step 5: Add One Field One your Login Form
-------------------------

You need now to add one field one your login form.

```html
<form>

    <label for="otp">Code OTP(optionnal if you haven't accept 2factorAuth)</label>
    <input type="text" name="otp">
    
</form>
```

Step 6: Enjoy
-------------------------

- You can now try it. First create a user that accepts the **2Factor Authentication**.

- Then he will receive a **QRCode** on his email. Scan it with an otp app like **Google Authenticator** [dowload it here](https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=fr)

- Finally go on the login page and enter the generated code on your app to connect.

- That's magic right ?! Hope you like it; feel free to give us feed backs and report bugs. We'd like to know your opinion. 

Configuration
-------------------------

If you want to redefine default configuration add this to your `app/config/config.yml`

```yaml
lahthony_otp_auth:
    digest_algo:
        sha1 #algorithm
    digit:
        6 #the output will generate 6 digit 
    period:
        30 #period for the timer
    issuer:
        'your_website_name'
    image:
         null
    sender_address:
        'yourwebsiteaddress@gmail.com'
```
