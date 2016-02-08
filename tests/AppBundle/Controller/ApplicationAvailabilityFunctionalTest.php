<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Description of ApplicationAvailabilityFunctionalTest
 *
 * @author reda
 */
class ApplicationAvailabilityFunctionalTest extends WebTestCase
{
    
//    private static $userRepositoryMock; 
    
    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->loadFixtures(array(
            'AppBundle\DataFixtures\ORM\LoadUserData',
        ));
    }
    
    /**
     * @dataProvider publicUrlProvider
     * @param type $url
     */
    public function testPublicUrls($url) 
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertTrue(
                $client->getResponse()->isSuccessful(),
                sprintf('The %s public URL loads correctly.', $url)
            );
    }
    
    /**
     * @dataProvider secureUrlProvider
     * @param type $url
     */
    public function testSecureUrls($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isRedirect());

        $this->assertEquals(
            'http://localhost/login',
            $client->getResponse()->getTargetUrl(),
            sprintf('The %s secure URL redirects to the login form.', $url)
        );
    }
    
    public function testUserLogin()
    {   
//        self::$userRepositoryMock = $this->getMockBuilder(UserRepository::class)
//            ->disableOriginalConstructor()
//            ->getMock();
//        
//        $mockedUser = new User();
//        $mockedUser->setUsername('test');
//        $mockedUser->setEmail('test@test.com');
//        $mockedUser->setId('10');
//        
//        self::$userRepositoryMock
//            ->expects($this->any())
//            ->method("find")
//            ->will($this->returnValue($mockedUser));
        
        $client = self::createClient();

//        $client->getContainer()->set('app.user_repository', self::$userRepositoryMock);

        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Login')->form();
        
        $form['_username'] = 'admin';
        $form['_password'] = 'admin';
        
//        $client->getContainer()->set('app.user_repository', self::$userRepositoryMock);

        $crawler = $client->submit($form);

        while ($client->getResponse()->isRedirect())
        {   
//            $client->getContainer()->set('app.user_repository', self::$userRepositoryMock);
            $crawler = $client->followRedirect();
        }
        $this->assertTrue(
                $client->getResponse()->isSuccessful(),
                sprintf('The %s',$client->getResponse()->getContent())
                );
        
        $this->assertGreaterThan(0, $crawler->filter('html:contains("User Profile")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("admin")')->count());
    }
    
    public function testUserSignUp()
    {
        $client = self::createClient();
        
        $crowler = $client->request('GET', '/signup');
        
        $form = $crowler->selectButton('Save')->form();
        
        $form['user[username]'] = 'user_1';
        $form['user[email]'] = 'user_1@email.com';
        $form['user[password][first]'] = 'user_1_password';
        $form['user[password][second]'] = 'user_1_password';
        
        $crowler = $client->submit($form);
        
        while ($client->getResponse()->isRedirect())
        {   
            $crowler = $client->followRedirect();
        }
        
        $this->assertTrue(
                $client->getResponse()->isSuccessful(),
                sprintf('The %s',$client->getResponse()->getContent())
                );
        
        $this->assertGreaterThan(0, $crowler->filter('html:contains("User Profile")')->count());
        $this->assertGreaterThan(0, $crowler->filter('html:contains("user_1")')->count());
    }
    
    public function testUserUpdate()
    {
        $client = static::createClient();
        
        $userRepository = $client->getContainer()->get('app.user_repository');
        
        
        $user = $userRepository->findOneByUsername('admin');
//        $user->setUsername('admin');
//        $user->setPassword('$2a$08$jHZj/wJfcVKlIwr5AvR78euJxYK7Ku5kURNhNx.7.CSIJ3Pq6LEPC');
        
        
        $this->login($user, $client);
        
        
        
        $crowler = $client->request('GET', '/user/'.$user->getId().'/update');
        
        $this->assertGreaterThan(0, $crowler->filter('html:contains("Update User Profile")')->count());
        
        $form = $crowler->selectButton('Save')->form();
        
        $form['update_user[email]'] = 'test_new@email.com';
        
        $crowler = $client->submit($form);
        
        while($client->getResponse()->isRedirect())
        {
            $crowler = $client->followRedirect();
        }
        
        $this->assertTrue(
                $client->getResponse()->isSuccessful(),
                sprintf('The %s',$client->getResponse()->getContent())
                );
        
        $this->assertGreaterThan(0, $crowler->filter('html:contains("User Profile")')->count());
        $this->assertGreaterThan(0, $crowler->filter('html:contains("test_new@email.com")')->count());
        
        
    }
    public function testUserAlreadyHasAMemberShipLinkRedirection(){
        
        $client = static::createClient();
        
        $crowler = $client->request('Get', '/signup');
        
        $link = $crowler
                ->filter('a:contains("membership")')
                ->eq(0)
                ->link()
            ;
        
        $crowler =  $client->click($link);
        
        // check if redirected page is login page (should contains form with action= /login_check)
        
        $loginFormAction = $crowler->filter('form')->eq(0)->attr('action');
        
        $this->assertEquals($loginFormAction, '/login_check');
    }
    
    private function login(User $user , $client){
        
        // dummy call to bypass the hasPreviousSession check
        $crawler = $client->request('GET', '/');
        
        $firewall = 'main';
        $token = new UsernamePasswordToken($user, $user->getPassword(), $firewall, $user->getRoles());

        $session = $client->getContainer()->get('session');
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();
    }
    
    public function publicUrlProvider() 
    {
        return array(
            array('/login'),
            array('/signup'),
        );
    }
    
    public function secureUrlProvider() 
    {
        return array(
            array('/user/1'),
            array('/user/1/update'),
        );
    }

}
