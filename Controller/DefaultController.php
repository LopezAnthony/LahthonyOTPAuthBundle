<?php

namespace LahthonyOTPAuthBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('LahthonyOTPAuthBundle:Default:index.html.twig');
    }
}
