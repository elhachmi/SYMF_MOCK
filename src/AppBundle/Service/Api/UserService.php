<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Service\Api;

use AppBundle\Entity\User;

/**
 *
 * @author reda
 */
interface UserService {
    
    public function createUser(User $user);
    public function updateUser(User $user);
    public function findUser($id);
    public function uploadUserAvatar(User $user);
    public function changeUserPassword(User $user, $newPassword);
    
}
