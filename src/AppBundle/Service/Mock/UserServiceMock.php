<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Service\Mock;

use AppBundle\Entity\User;
use AppBundle\Service\Api\UserService;

/**
 * Description of UserServiceMock
 *
 * @author reda
 */
class UserServiceMock implements UserService{
    
    protected $user;
    
    public function __construct() {
        $user = new User();
        
        $user->setId("1");
        $user->setUsername("test");
        $user->setEmail('test@test.test');
        $user->setPassword("pass");
        $user->setAvatarUrl("default_avatar.jpg");
        
        $this->user = $user;
    }

    public function createUser(User $user) {
        return $this->user;
    }

    public function findUser($id) {
        
        return $this->user;
    }

    public function updateUser(User $user) {
        return $this->user;
    }

}
