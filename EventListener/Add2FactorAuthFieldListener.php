<?php

namespace LahthonyOTPAuthBundle\EventListener;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Add2FactorAuthFieldListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $user = $event->getData();
        $form = $event->getForm();

        if (!$user || null === $user->getId()) {
            $form->add('OTP2Auth', ChoiceType::class,
                array(
                    'choices'  => array(
                        'No' => false,
                        'Yes' => true,
                    ),
                    'mapped' => true
                )
            );
        }
    }
}