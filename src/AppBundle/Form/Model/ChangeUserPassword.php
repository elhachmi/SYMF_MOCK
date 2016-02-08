<?php

namespace AppBundle\Form\Model;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Description of ChangePassword.
 *
 * @author RAMRAMI Mohamed
 */
class ChangeUserPassword
{
    /**
      * @SecurityAssert\UserPassword(
      *     message = "Wrong value for your current password"
      * )
      */
    protected $oldPassword;

     /**
      * @Assert\Length(min=6, max=255)
      * @Assert\NotBlank()
      */
    protected $newPassword;

    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    public function getNewPassword()
    {
        return $this->newPassword;
    }

    public function setOldPassword($oldPassword)
    {
        $this->oldPassword = $oldPassword;
    }

    public function setNewPassword($newPassword)
    {
        $this->newPassword = $newPassword;
    }
}
