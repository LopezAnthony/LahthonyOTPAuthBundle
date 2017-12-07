<?php

namespace LahthonyOTPAuthBundle\Controller;

use LahthonyOTPAuthBundle\Form\RecoveryType;
use LahthonyOTPAuthBundle\Manager\RecoveryManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RecoveryController extends Controller
{
    public function recoveryAction(Request $request, $email, RecoveryManager $recoveryManager)
    {
        $entity = $this->getParameter('otp.user.entity');
        $manager = $this->getDoctrine()->getManager();
        $user = $manager->getRepository($entity)->findOneByEmail($email);

        $form = $this->createForm(RecoveryType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $secret = $request->request->get('verifyKey');
            if (false === $recoveryManager->verifyOTPKey($secret, $user)) {
                throw new \Exception('You\'ve entered a wrong secret code.');
            }
            $recoveryManager->resetOTP($user);
            $manager->flush();

            $this->addFlash('reset', 'You\'ve reset your OTP Authentication, you can login normally then go to your profile page to ask for a new one !');
        }

        return $this->render('@LahthonyOTPAuth/recovery.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
