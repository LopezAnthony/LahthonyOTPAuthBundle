<?php

namespace LahthonyOTPAuthBundle\Controller;

use LahthonyOTPAuthBundle\Form\Type\RecoveryType;
use LahthonyOTPAuthBundle\Manager\RecoveryManager;
use LahthonyOTPAuthBundle\Service\TwigMailGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\Request;

class RecoveryController extends Controller
{
    public function askAction(Request $request, TwigMailGenerator $mailGenerator, \Swift_Mailer $mailer)
    {
        $defaultData = array('message' => 'Please Enter your Email to reset your OTP Authentication.');
        $form = $this->createFormBuilder($defaultData)
            ->add('email', EmailType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = $mailGenerator->getMessage($data['email']);
            $message->setFrom('toto@gmail.com');
            $message->setTo($data['email']);
            $mailer->send($message);

            $this->addFlash('reset', 'You\'ve received an email go check it out.');

            return $this->redirect('/');
        }

        return $this->render('@LahthonyOTPAuth/ask.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    public function recoveryAction(Request $request, $email, RecoveryManager $recoveryManager)
    {
        $entity = $this->getParameter('otp.user.entity');
        $manager = $this->getDoctrine()->getManager();
        $user = $manager->getRepository($entity)->findOneByEmail($email);

        $form = $this->createForm(RecoveryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $secret = $request->request->get('verifyKey');
            if (false === $recoveryManager->verifyOTPKey($secret, $user)) {
                throw new \Exception('You\'ve entered a wrong secret code.');
            }
            $recoveryManager->resetOTP($user);
            $manager->flush();

            $this->addFlash('reset', 'You\'ve reset your OTP Authentication, you can login normally then go to your profile page to ask for a new one !');

            return $this->redirect('/');
        }

        return $this->render('@LahthonyOTPAuth/recovery.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
