<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login_route")
     */
    public function loginAction() {
        $authenticationUtils = $this->get('security.authentication_utils');        
        $error = $authenticationUtils->getLastAuthenticationError(); // get the login error if there is one        
        $lastUsername = $authenticationUtils->getLastUsername(); // last username entered by the user
        
        return $this->render( 'security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error' => $error,
            )
        );
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction() {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }
    
    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction() {
        // this controller will not be executed,
        // as the route is handled by the Security system
    }    
}
