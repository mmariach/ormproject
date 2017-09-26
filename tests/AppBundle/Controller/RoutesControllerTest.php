<?php

namespace Tests\AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class RoutesControllerTest: testing main routes
 */
class RoutesControllerTest extends WebTestCase
{
    public function testBlog()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/blog/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            'Title',
            $client->getResponse()->getContent()
        );
    }

    public function testProducts()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/orm/show/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            'Category',
            $client->getResponse()->getContent()
        );
        $crawler = $client->request('GET', '/orm/show/15');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            'Category',
            $client->getResponse()->getContent()
        );
    }

    public function testLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login2');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            'Username',
            $client->getResponse()->getContent()
        );
/*
        //get Form object
        $form = $crawler->selectButton('user[submit]')->form();

        $crawler = $client->submit($form, array('user[_username]' => 'test', 'user[_password]' => 'test'));
        $this->assertContains(
            'User Information',
            $client->getResponse()->getContent()
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertRegexp(
            '/This value should not be blank/',
            $client->getResponse()->getContent()
        );
*/

    }

    public function testRegister()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register2');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains(
            'Username',
            $client->getResponse()->getContent()
        );
    }

}