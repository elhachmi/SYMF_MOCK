<?php

namespace Tests\AppBundle\Form\Model;

use Symfony\Component\Validator\Validation;
use AppBundle\Form\Model\ChangeUserPassword;

/**
 * This Test class test only the validation constraint for the new password.
 * The old password is handled by symfony's security frameword
 * 
 */
class ChangeUserPasswordValidationTest extends \PHPUnit_Framework_TestCase {
    
    private $validator ;
    private $changeUserPasswordModel;
    
    const ATTR_PASSWORD = "newPassword";
    
    const PASSWORD_MIN_LENGTH = 6;
        
    protected function setUp() 
    {
        $this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $this->changeUserPasswordModel = new ChangeUserPassword();
    }


    public function testPasswordIsRequired() {
        $errors = $this->validator->validateProperty($this->changeUserPasswordModel, self::ATTR_PASSWORD);
        $this->assertTrue(count($errors) > 0);

        $this->changeUserPasswordModel->setNewPassword("********");
        $errors = $this->validator->validateProperty($this->changeUserPasswordModel, self::ATTR_PASSWORD);
        $this->assertEquals(0, count($errors));
    }
    
    public function testPasswordLengthShouldBeGreaterThanMinLength()
    {
        $this->changeUserPasswordModel->setNewPassword("***");
        $errors = $this->validator->validateProperty($this->changeUserPasswordModel, self::ATTR_PASSWORD);
        $this->assertTrue( strlen($this->changeUserPasswordModel->getNewPassword()) < self::PASSWORD_MIN_LENGTH ); 
        $this->assertTrue(count($errors) > 0);

        $this->changeUserPasswordModel->setNewPassword("******");
        $errors = $this->validator->validateProperty($this->changeUserPasswordModel, self::ATTR_PASSWORD);
        $this->assertTrue( strlen($this->changeUserPasswordModel->getNewPassword()) >= self::PASSWORD_MIN_LENGTH); 
        $this->assertEquals(0, count($errors));         
    }    

}
