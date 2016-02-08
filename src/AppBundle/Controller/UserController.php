<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\UserType;
use AppBundle\Form\Type\UpdateUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserController extends Controller 
{

    /**
     * @Route("/signup", name="signup")
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

                //Authenticate User so he can access to profile
                $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
                $this->get("security.token_storage")->setToken($token); //now the user is logged in
                $this->get('session')->set('_security_secured_area', serialize($token));
                
                return $this->redirectToRoute('user_info', array('id' => $user->getId()) );
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
     * @Route("user/{id}" ,name="user_info") 
     */
	function getUserInfo($id){

//		 $user = $this->getDoctrine()
//                      ->getRepository('AppBundle:User')
//                      ->find($id);
                
                $userService = $this->get('app.user_service');
                $user = $userService->findUser($id);
            
		return $this->render('profile.html.twig',array(
			"user"=>$user));

	}

    
    /**
     * @Route("/user/{id}/update", name="update_profile")
     */
    public function updateProfileAction($id, Request $request)
    {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);         
        $oldEmail = $user->getEmail(); // TODO find a way to get all modified fields        
        $oldUserAvatarUrl = $user->getAvatarUrl();
        
        try{
            $userAvatar = new File('uploads/avatars/' . $oldUserAvatarUrl);
        } catch (\Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException $ex) {
            $userAvatar = null;
        }        
        
        $user->setAvatarUrl($userAvatar); //Required by Symfony form FileType that need a File object not string
        
        $form = $this->createForm(UpdateUserType::class, $user);
        $form->handleRequest($request);
        
        if($form->isSubmitted()){
            if ($oldEmail != $user->getEmail() && $this->isUserEmailAlreadyExisted($user->getEmail())) {
                $form->addError(new FormError('Email already used'));
            }
            
            if($form->isValid()){
                if($user->getAvatarUrl() != null){
                    $this->uploadUserAvatar($user);
                }else{
                    $user->setAvatarUrl($oldUserAvatarUrl);
                }
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($user);
                $em->flush();    
                return $this->redirectToRoute("user_info", array("id" => $user->getId()));
            }
        }
        
        return $this->render('update_profile.html.twig',array(
            "user" => $user,
            "oldUserAvatarUrl" => $oldUserAvatarUrl,
            "form" => $form->createView(),
        ));        
    }
}

