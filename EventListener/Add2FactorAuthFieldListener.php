<?php

namespace LahthonyOTPAuthBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class Add2FactorAuthFieldListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [FormEvents::PRE_SET_DATA => 'preSetData'];
    }

    public function preSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        if (!$user || null === $user->getId()) {
            $form->add('OTP2Auth', ChoiceType::class,
                [
                    'choices'  => [
                        'No'  => false,
                        'Yes' => true,
                    ],
                    'mapped' => true,
                ]
            );
        }
    }
}
