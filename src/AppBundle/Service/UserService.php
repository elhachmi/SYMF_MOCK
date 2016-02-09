<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;

/**
 * Description of UserService
 *
 * @author reda
 */
class UserService implements Api\UserService{
    
    const USER_AVATAR_DIR = "/../web/uploads/avatars";
    const AVATAR_NAME_POSTFIX = "_avatar.";
    
    protected $userRepository;
    protected $passwordEncoder;
    protected $kernelRootDir;


    public function __construct(UserRepository $userRepository, $passwordEncoder, $kernelRootDir) 
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->kernelRootDir = $kernelRootDir;
    }


    public function createUser(User $user)
    {
        // Encode pasword ...
        $password = $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        
        // Store avatar
        $this->uploadUserAvatar($user);
        
        // create user ...
        $this->userRepository->createUser($user);
        
        return $user;
    }
    
    public function updateUser(User $user)
    {
        // update user ...
        $this->userRepository->updateUser($user);
        return $user;
    }
    
    public function findUser($id) 
    {
        return $this->userRepository->find($id);
    }

    public function uploadUserAvatar(User $user) 
    {
        /**
         * @var UploadedFile $file
         */
        $file = $user->getAvatarUrl();

        if ($file != null) {
            $username = trim($user->getUsername());
            $username = preg_replace('/\s+/', '_', $username);

            $fileName = $username.'_avatar.'.$file->guessExtension();

            $avatarsDir = $this->kernelRootDir .''. self::USER_AVATAR_DIR;

            $file->move($avatarsDir, $fileName);

            $user->setAvatarUrl($fileName);
        }        
    }

    public function changeUserPassword(User $user, $newPassword) 
    {
        // Encode pasword ...
        $password = $this->passwordEncoder->encodePassword($user, $newPassword);
        $user->setPassword($password);   
        
        //Update User
        $this->updateUser($user);
    }

}
