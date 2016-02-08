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
class UserService {
    
    const USER_AVATAR_DIR = "/../web/uploads/avatars";
    const AVATAR_NAME_POSTFIX = "_avatar.";
    
    protected $userRepository;


    public function __construct(UserRepository $userRepository) 
    {
        $this->userRepository = $userRepository;
    }


    public function createUser(User $user)
    {
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
    
    public function isUserEmailAlreadyExisted($email)
    {
        // validate email ...

//        $repository = $this->em->getRepository('AppBundle:User');
        
        $usersWithSameEmail = $this->userRepository->findByEmail($email);
         
        if (count($usersWithSameEmail) != null) {
             return true;
        }
        return false;
    }
    
    public function isUserUsernameAlreadyExisted($username)
    {
        // validate username ...

//        $repository = $this->em->getRepository('AppBundle:User');
        
        $usersWithSameUsername = $this->userRepository->findByEmail($username);
         
        if (count($usersWithSameUsername) != null) {
             return true;
        }
        return false;
    }
    

    public function findUser($id) 
    {
        return $this->userRepository->find($id);
    }

}
