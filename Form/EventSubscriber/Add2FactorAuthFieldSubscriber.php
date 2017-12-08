<?php

namespace LahthonyOTPAuthBundle\Form\EventSubscriber;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Add2FactorAuthFieldSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => [
            ['preSetData'],
            ['hydrateOTP2AuthField'],
        ],
            FormEvents::SUBMIT => 'formProcess',
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
        if ($user->getId()) {
            $form->add('OTP2Auth', ChoiceType::class,
                array(
                    'choices' => array(
                        'Yes' => true,
                        'No' => false,
                    ),
                    'choice_attr' => function ($val, $key) use ($user) {
                        if ($user->getSecretAuthKey() && 'Yes' === $key) {
                            return ['selected' => null];
                        } elseif (null === $user->getSecretAuthKey() && 'No' === $key) {
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
    }

    public function formProcess(FormEvent $event)
    {
        $user = $event->getData();
        if (false === $user->getOTP2Auth()) {
            $user->setSecretAuthKey(null);
            $user->setRecoveryKey(null);
        }
        if ($user->getID() && $user->getOTP2Auth() && null === $user->getSecretAuthKey()) {
            $user->setSecretAuthKey(true);
        }
    }
}
