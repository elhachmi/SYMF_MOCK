<?php

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    
    private $repository;

    /**
     * {@inheritDoc}
     */
    protected function setUp() {
        self::bootKernel(array('environment' => 'test'));
        $this->em = static::$kernel->getContainer()
                ->get('doctrine')
                ->getManager();
        
        $this->em->beginTransaction();
        $this->repository = $this->em->getRepository("AppBundle:User");
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() {
        $this->em->rollback();
        
        parent::tearDown();
        $this->em->close();
    }

    public function testCreateUser() {

        $user = new User();
        $user->setUsername("user3");
        $user->setPassword("$2y$13$9z7J/8pHLBd4OC7sJQZiFe.C79rBWzByn0Z9V4Viu5hESJ2E4cvj2");
        $user->setEmail("user3@test.com");
        $user->setAvatarUrl("user3_avatar");
        
        $this->repository->createUser($user);

        $this->assertTrue( count($this->repository->findAll()) > 1); //The Test DB contains one user already (admin for authentication)
        
        $newUser = $this->repository->find($user->getId());
        $this->assertNotNull($newUser);
        $this->assertEquals( $newUser->getId(), $user->getId());
        $this->assertEquals( $newUser->getUsername(), $user->getUsername());
        $this->assertEquals( $newUser->getEmail(), $user->getEmail());
        $this->assertEquals( $newUser->getPassword(), $user->getPassword());
        // TODO find a way to compare two objects
        
    }

    public function testUpdateUser() {

        $user = new User();
        $user->setUsername("user1");
        $user->setPassword("$2y$13$9z7J/8pHLBd4OC7sJQZiFe.C79rBWzByn0Z9V4Viu5hESJ2E4cvj2");
        $user->setEmail("username1@test.com");
        $user->setAvatarUrl("user3_avatar.jpg");
        
        $this->em->persist($user); // Not using createUser from our repository, not safe
        $this->em->flush();
        
        $newUser = $this->repository->find($user->getId());        
        $newUsername = "newUsername";
        $newUser->setUsername($newUsername);
        $this->repository->updateUser($newUser);
        
        $updatedUser = $this->repository->find($newUser->getId());
        $this->assertNotNull($updatedUser);
        $this->assertEquals( $updatedUser->getId(), $newUser->getId());
        $this->assertEquals( $updatedUser->getUsername(), $newUser->getUsername());
        $this->assertEquals( $updatedUser->getEmail(), $newUser->getEmail());
        $this->assertEquals( $updatedUser->getPassword(), $newUser->getPassword());
        // TODO find a way to compare two objects        
    }

}
