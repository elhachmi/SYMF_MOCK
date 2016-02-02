<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\User;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        
        $user = new User();
        $user->setUserName("reda");
        $user->setPassword("password");
        $user->setEmail("reda@email.com");
        $user->setAvatarUrl("file.jpg");
        $validator = $this->get('validator');
        $errors = $validator->validate($user);
        
        if (count($errors) > 0) {

            $errorsString = (string) $errors;
            throw new \Exception("validation error".$errorsString);
        }

        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ]);
    }
}
