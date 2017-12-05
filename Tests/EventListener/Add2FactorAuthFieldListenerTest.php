<?php
/**
 * Created by PhpStorm.
 * User: Etudiant
 * Date: 01/12/2017
 * Time: 13:01.
 */

namespace LahthonyOTPAuthBundle\Tests\EventListener;

use LahthonyOTPAuthBundle\EventListener\Add2FactorAuthFieldListener;
use LahthonyOTPAuthBundle\Tests\TestUser;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;

class Add2FactorAuthFieldListenerTest extends TestCase
{
    /**
     * @var
     */
    private $form;

    /**
     * @var
     */
    private $event;

    public function setUp()
    {
        $this->form = $this->createMock(Form::class);

        $this->event = $this->getMockBuilder(FormEvent::class)
            ->setMethods(['getData', 'getForm'])
            ->disableOriginalConstructor()
            ->getMock()
        ;
    }

    public function testAdd2FactorAuthFieldListener()
    {
        $form = $this->form;

        $this->form
            ->expects($this->once())
            ->method('add')
            ->with($this->equalTo('OTP2Auth'),
                $this->equalTo(ChoiceType::class),
               $this->equalTo([
                    'choices' => array(
                        'No' => false,
                        'Yes' => true,
                    ),
                    'mapped' => true,
                   'attr' => [
                       'class' => 'browser-default',
                   ],
                ])
            );

        $this->event->method('getForm')
            ->willReturn($form);

        $a2f = new Add2FactorAuthFieldListener();
        $a2f->preSetData($this->event);
    }

    public function testNoAddFieldIfUserExist()
    {
        $user = new TestUser();
        $user->setId(1);

        $this->form
            ->expects($this->never())
            ->method('add');

        $this->event->method('getData')
            ->willReturn($user);

        $a2f = new Add2FactorAuthFieldListener();
        $a2f->preSetData($this->event);
    }

    public function testUserSubmitNoOTPAuthActivate()
    {
        $user = $this->createMock(TestUser::class);

        $user->method('getOTP2Auth')
            ->willReturn(false)
        ;
        $user
            ->expects($this->once())
            ->method('setSecretAuthKey')
            ->with(null)
        ;

        $this->event->method('getData')
            ->willReturn($user);

        $a2f = new Add2FactorAuthFieldListener();
        $a2f->formProcess($this->event);
    }

    public function testUserSubmitYesOTPAuthActivate()
    {
        $user = $this->createMock(TestUser::class);

        $user->method('getId')
            ->willReturn(12)
        ;
        $user->method('getOTP2Auth')
            ->willReturn(true)
        ;
        $user
            ->expects($this->once())
            ->method('getSecretAuthKey')
            ->willReturn(null)
        ;
        $user
            ->expects($this->once())
            ->method('setSecretAuthKey')
            ->with(true)
        ;

        $this->event->method('getData')
            ->willReturn($user);

        $a2f = new Add2FactorAuthFieldListener();
        $a2f->formProcess($this->event);
    }
}
