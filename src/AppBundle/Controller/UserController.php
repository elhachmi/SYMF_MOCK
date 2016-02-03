<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class UserController extends Controller
{

    

    /**
     * @Route("/signup")
     */
	function signUpAction(){

		return $this->render('register.html.twig');
	}
}