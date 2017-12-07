LahthonyOTPAuthBundle
============

[![Build Status](https://travis-ci.org/LopezAnthony/LahthonyOTPAuthBundle.svg?branch=master)](https://travis-ci.org/LopezAnthony/LahthonyOTPAuthBundle)
[![StyleCI](https://styleci.io/repos/112461062/shield?branch=master)](https://styleci.io/repos/112461062)
[![Coverage Status](https://coveralls.io/repos/github/LopezAnthony/LahthonyOTPAuthBundle/badge.svg?branch=master)](https://coveralls.io/github/LopezAnthony/LahthonyOTPAuthBundle?branch=master)

About :
--------------------------

This bundle permits to easy implements *2 factor authentication* in a symfony project. 

**Users** will then get **TOTP** authentication by using apps like `Google Authenticator`

Let's get started. Just go through the following steps.

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

Then you will need to update the `service.yml`

```yaml
    YourBundle\:
        resource: '../../src/YourBundle/*'
        #Remove the folder Entity From exclude folder 
        exclude: '../../src/YourBundle/{Repository,Tests}'
```

Step 3: Implements OTPAuthInterface
-------------------------

You need to implement the OTPAuthInterface on your User Entity commonly present in `src/AppBundle/Entity/User`. 

:warning: **Do not forget to generate getter setter.** :warning:

```php
<?php
//src/AppBundle/Entity/User

use LahthonyOTPAuthBundle\Model\OTPAuthInterface;
//...

class User implements OTPAuthInterface
{
    /**
     * This attribute needs to be stock in Database
     * @var string 
     * @ORM\Column(name="secret_auth_key", type="string", length=255, nullable=true)
     */
    private $secretAuthKey;

    /**
     * This attribute needs to be stocked in Database   
     * @var string
     * @ORM\Column(name="recovery_key", type="string", length=255, nullable=true)
     */
    private $recoveryKey;
    
    
    /**
     * This attribute will permit to do verification on user registration 
     * if he accepts 2Factor Authentication 
     * @var boolean
     */
    private $OTP2Auth;
    
  
    /**
     * !!! DO NOT FORGET TO GENERATE GETTER AND SETTER FOR THESE THREE ATTRIBUTES !!! 
     */
    
    //We'll need email and password for the OTP Authentication reset
    public function getEmail(){}
    public function getPassword(){}
        
}
```
:warning: **After that DO NOT FORGET to schema update:** :warning:

```console
$ php bin/console doctrine:schema:update --force
```


Step 4: Add one field to your `UserFormType`
-------------------------

We've made for you an eventsubscriber that permits you to add the required field easily on your`UserFormType`. 

You can add it on your UserEditType too if you want to permit your users to enable or disable OTP Authentication after he has registered.

Just do like so:

For User Registration:

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
}
```
For User Edit:
```php
//src/AppBundle/Form/UserEditType

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
}
```

Step 5: Update your Login Form And your homepage
-------------------------

You need now to add one field one your login form and the link to reset the authenticator

```html
<!--login login.html.twig -->
<form>
    <label for="otp">Code OTP(optionnal if you haven't accept 2factorAuth)</label>
    <input type="text" name="otp">
</form>
<a href="{{ path('lahthony_otp_ask_recovery') }}">I've lost my OTP Authenticator.</a>
```
```html
<!-- homepage index.html.twig -->
    <div class="flash-notice">
        {% for message in app.flashes('2factor') %}
            {{ message|raw }}
        {% endfor %}
        {% for message in app.flashes('reset') %}
            {{ message }}
        {% endfor %}
    </div>
```

Step 6: Import Routes
-------------------------

In your `routing.yml` import routes from our bundle :

```yaml
lahthony_otp_auth_recovery:
    resource: "@LahthonyOTPAuthBundle/Resources/config/routing.xml"
```

Step 7: Enjoy
-------------------------

- You can now try it. First create a user that accepts the **2Factor Authentication**.

- Then a flash message will appears on your homepage with the **QRCode** and the **Recovery Pass**.
 
    :warning: Don't forget to wrote it down the **Recovery Pass** if you want to recover an account that has lost its authenticator. :warning:

    Scan the **QRCode** with an otp app like **Google Authenticator** [dowload it here](https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=fr)

- Go on the login page and enter the generated code on your app to connect.

- You can now update it from your user edit ask to disable it.

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
    roles: []
```
