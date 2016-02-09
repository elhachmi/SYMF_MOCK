<?php

use DoctrineExtensions\PHPUnit\OrmTestCase;
use AppBundle\Entity\User;
//use AppBundle\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;

class UserRepositoryTest extends OrmTestCase {

    /**
     * @var EntityManager
     */
    protected static $em = null;

    public static function setUpBeforeClass() {
        $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/../../../src"), true, null, null, false);
        $connectionOptions = array('driver' => 'pdo_sqlite', 'memory' => true);
        // obtaining the entity manager
        self::$em = EntityManager::create($connectionOptions, $config);
        $schemaTool = new SchemaTool(self::$em);
        $cmf = self::$em->getMetadataFactory();
        $classes = $cmf->getAllMetadata();
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);
    }

    protected function tearDown() {
        self::$em->clear();
        parent::tearDown();
    }

    protected function createEntityManager() {
        return self::$em;
    }

    protected function getDataSet() {
        return $this->createFlatXmlDataSet(__DIR__ . "/../DataSets/dataset.xml");
    }

    public function testCreateUser() {

        $user = new User();
        $user->setUsername("user3");
        $user->setPassword("$2y$13$9z7J/8pHLBd4OC7sJQZiFe.C79rBWzByn0Z9V4Viu5hESJ2E4cvj2");
        $user->setEmail("user3@test.com");
        $user->setAvatarUrl("user3_avatar");

        //$repository = new UserRepository();
        //$repository->createUser($user);
        //$this->assertEquals(3, $user->getId());
        //$this->assertEquals(, );
    }

    public function testUpdateUser() {

        $user = new User();
        $user->setId(1);
        $user->setUsername("user1");
        $user->setPassword("$2y$13$9z7J/8pHLBd4OC7sJQZiFe.C79rBWzByn0Z9V4Viu5hESJ2E4cvj2");
        $user->setEmail("username1@test.com");
        $user->setAvatarUrl("user3_avatar.jpg");

        //$repository = new UserRepository();
        //$repository->updateUser($user);
        //$this->assertEquals(, );
        //$this->assertEquals(, );
    }

}
