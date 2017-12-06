<?php

namespace LahthonyOTPAuthBundle\Controller;

use LahthonyOTPAuthBundle\Form\RecoveryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RecoveryController extends Controller
{
    public function recoveryAction(Request $request, $email)
    {
        $entity = $this->getParameter('otp.user.entity');
        $manager = $this->getDoctrine()->getManager();
        $userRepository = $manager->getRepository($entity);

        $user = $userRepository->findOneByEmail($email);

        $form = $this->createForm(RecoveryType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return true;
        }

        return $this->render('registration.html.twig', array('form' => $form->createView()));
    }
}
