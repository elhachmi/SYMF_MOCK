<?php

namespace Tests\AppBundle\Entity;

use Symfony\Component\Validator\Validation;
use AppBundle\Entity\User;

class UserValidationTest extends \PHPUnit_Framework_TestCase
{
    private $validator;
    private $user;

    const ATTR_EMAIL = 'email';
    const ATTR_USERNAME = 'username';
    const ATTR_PASSWORD = 'password';

    const USERNAME_MIN_LENGTH = 3;
    const PASSWORD_MIN_LENGTH = 6;

    protected function setUp()
    {
        $this->validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $this->user = new User();
    }

    public function testEmailShouldBeValid()
    {
        $this->user->setEmail('wrongEmail');
        $errors = $this->validator->validateProperty($this->user, self::ATTR_EMAIL);
        $this->assertEquals(1, count($errors));

        $this->user->setEmail('good.email@gmail.com');
        $errors = $this->validator->validateProperty($this->user, self::ATTR_EMAIL);
        $this->assertEquals(0, count($errors));
    }

    public function testEmailIsRequired()
    {
        $errors = $this->validator->validateProperty($this->user, self::ATTR_EMAIL);
        $this->assertTrue(count($errors) > 0);
    }

    public function testUsernameIsRequired()
    {
        $errors = $this->validator->validateProperty($this->user, self::ATTR_USERNAME);
        $this->assertTrue(count($errors) > 0);

        $this->user->setUsername('someUserName');
        $errors = $this->validator->validateProperty($this->user, self::ATTR_USERNAME);
        $this->assertEquals(0, count($errors));
    }

    public function testUsernameLengthShouldBeGreaterThanMinLength()
    {
        $this->user->setUsername('us');
        $errors = $this->validator->validateProperty($this->user, self::ATTR_USERNAME);
        $this->assertTrue(strlen($this->user->getUsername()) < self::USERNAME_MIN_LENGTH);
        $this->assertTrue(count($errors) > 0);

        $this->user->setUsername('user');
        $errors = $this->validator->validateProperty($this->user, self::ATTR_USERNAME);
        $this->assertTrue(strlen($this->user->getUsername()) >= self::USERNAME_MIN_LENGTH);
        $this->assertEquals(0, count($errors));
    }

    public function testPasswordIsRequired()
    {
        $errors = $this->validator->validateProperty($this->user, self::ATTR_PASSWORD);
        $this->assertTrue(count($errors) > 0);

        $this->user->setPassword('********');
        $errors = $this->validator->validateProperty($this->user, self::ATTR_PASSWORD);
        $this->assertEquals(0, count($errors));
    }

    public function testPasswordLengthShouldBeGreaterThanMinLength()
    {
        $this->user->setPassword('***');
        $errors = $this->validator->validateProperty($this->user, self::ATTR_PASSWORD);
        $this->assertTrue(strlen($this->user->getPassword()) < self::PASSWORD_MIN_LENGTH);
        $this->assertTrue(count($errors) > 0);

        $this->user->setPassword('******');
        $errors = $this->validator->validateProperty($this->user, self::ATTR_PASSWORD);
        $this->assertTrue(strlen($this->user->getPassword()) >= self::PASSWORD_MIN_LENGTH);
        $this->assertEquals(0, count($errors));
    }
}
