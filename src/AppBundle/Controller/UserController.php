<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\UserType;
use AppBundle\Form\Type\UpdateUserType;
use AppBundle\Form\Type\ChangeUserPasswordType;
use AppBundle\Form\Model\ChangeUserPassword;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserController extends Controller
{
    /**
     * @Route("/signup", name="signup")
     * @Method({"GET", "POST"})
     */
    public function signUpAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // create user and store the avatar ...
            $this->get("app.user_service")->createUser($user);

            //Authenticate User so he can access to profile
            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
            $this->get('security.token_storage')->setToken($token); //now the user is logged in
            $this->get('session')->set('_security_main', serialize($token));

            return $this->redirectToRoute('user_info', array('id' => $user->getId()));
        }

        return $this->render(
            'register.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @Route("user/{id}" ,name="user_info")
     */
    public function getUserInfo($id)
    {
        $user = $this->get('app.user_service')->findUser($id);

        return $this->render(
            'profile.html.twig',
            array(
                'user' => $user,
            )
        );
    }

    /**
     * @Route("/user/{id}/update", name="update_profile")
     */
    public function updateProfileAction($id, Request $request)
    {
        $user = $this->get('app.user_service')->findUser($id);
        $oldUserAvatarUrl = $user->getAvatarUrl();

        try {
            $userAvatar = new File('uploads/avatars/'.$oldUserAvatarUrl);
        } catch (\Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException $ex) {
            $userAvatar = null;
        }

        $user->setAvatarUrl($userAvatar); //Required by Symfony form FileType that need a File object not string

        $form = $this->createForm(UpdateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getAvatarUrl() != null) {
                $this->get("app.user_service")->uploadUserAvatar($user);
            } else {
                $user->setAvatarUrl($oldUserAvatarUrl);
            }
            $this->get('app.user_service')->updateUser($user);

            return $this->redirectToRoute('user_info', array('id' => $user->getId()));
        }

        return $this->render(
            'update_profile.html.twig',
            array(
                'user' => $user,
                'oldUserAvatarUrl' => $oldUserAvatarUrl,
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @Route("/user/{id}/change_password", name="change_password")
     */
    public function changePasswordAction($id, Request $request)
    {
        $user = $this->get('app.user_service')->findUser($id);

        $changeUserPasswordModel = new ChangeUserPassword();
        $form = $this->createForm(ChangeUserPasswordType::class, $changeUserPasswordModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {            
            $this->get('app.user_service')->changeUserPassword($user, $changeUserPasswordModel->getNewPassword());

            return $this->redirectToRoute('user_info', array('id' => $user->getId()));
        }

        return $this->render(
            'change_password.html.twig',
            array(
                'user' => $user,
                'form' => $form->createView(),
            )
        );
    }
}
