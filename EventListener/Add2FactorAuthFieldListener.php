<?php

namespace LahthonyOTPAuthBundle\EventListener;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Add2FactorAuthFieldListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => [
            ['preSetData'],
            ['hydrateOTP2AuthField']
        ],
            FormEvents::PRE_SUBMIT => 'formProcess',
        );
    }

    public function preSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        if (!$user || null === $user->getId()) {
            $form->add('OTP2Auth', ChoiceType::class,
                array(
                    'choices' => array(
                        'No' => false,
                        'Yes' => true,
                    ),
                    'mapped' => true,
                    'attr' => [
                        'class' => 'browser-default',
                    ],
                )
            );
        }
    }

    public function hydrateOTP2AuthField(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        if ($user->getId())
        {
            $form->add('OTP2Auth', ChoiceType::class,
                array(
                    'choices' => array(
                        'Yes' => true,
                        'No' => false,
                    ),
                    'choice_attr' => function($val, $key) use ($user)
                    {
                        if(null !== $user->getSecretAuthKey() && 'Yes' === $key)
                        {
                            return ['selected' => null];
                        }elseif( 'No' === $key)
                        {
                            return ['selected' => null];
                        }
                        return [];
                    },
                    'mapped' => true,
                    'attr' => [
                        'class' => 'browser-default',
                    ],
                )
            );
        }

        if($user->getId() &&  null !== $user->getSecretAuthKey())
        {
            $form->add('otp', TextType::class,
                [
                    'mapped' => false
                ]
            );
        }
    }

    public function formProcess(FormEvent $event)
    {
    }

}
