<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller 
{

    /**
     * @Route("/signup")
     * @Method({"GET", "POST"})
     */
    function signUpAction(Request $request) 
    {

        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            
            if ($this->isUserEmailAlreadyExisted($user->getEmail())) {
                $form->addError(new FormError('email already used'));
            }
            if ($this->isUsernameAlreadyExisted($user->getUsername())) {
                $form->addError(new FormError('username already used'));
            }

            if ($form->isValid()) {
                
                // Upload user avatar ...
                
                $this->uploadUserAvatar($user);
                
                // Encode pasword ...

                $password = $this->get('security.password_encoder')
                        ->encodePassword($user, $user->getPassword());

                $user->setPassword($password);

                // create user ...
                
                $em = $this->getDoctrine()->getManager();

                $em->persist($user);

                $em->flush();

                return $this->redirect($this->generateUrl('user_info', array('id' => $user->getId())));
            }
        }
        
        return $this->render('register.html.twig', array(
                    'form' => $form->createView(),
        ));
    }
    
    private function uploadUserAvatar(User $user) 
    {

        /** @var UploadedFile $file */
        $file = $user->getAvatarUrl();

        if ($file != null) {
            
            $username = $user->getUsername();
            
            $username = trim($username);
            
            $username = preg_replace('/\s+/', '_', $username);
            
            $fileName = $username. '_avatar.' . $file->guessExtension();

            $avatarsDir = $this->container->getParameter('kernel.root_dir') . UserType::USER_AVATAR_DIR;

            $file->move($avatarsDir, $fileName);

            $user->setAvatarUrl($fileName);
        }
    }
    
    private function isUsernameAlreadyExisted($username)
    {
        // validate username ...

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        
        $usersWithSameUsername = $repository->findByUsername($username);
         
        if (count($usersWithSameUsername) != null) {
             return true;
        }
        return false;
    }
    
    private function isUserEmailAlreadyExisted($email)
    {
        // validate email ...

        $repository = $this->getDoctrine()->getRepository('AppBundle:User');
        
        $usersWithSameEmail = $repository->findByEmail($email);
         
        if (count($usersWithSameEmail) != null) {
             return true;
        }
        return false;
    }
    
    
    /**
     * @Route("user/{id}",name="user_info" )
     */
	function getUserInfo($id){

		 $user = $this->getDoctrine()
                      ->getRepository('AppBundle:User')
                      ->find($id);

		return $this->render('profile.html.twig',array(
			"user"=>$user));

	}
}

