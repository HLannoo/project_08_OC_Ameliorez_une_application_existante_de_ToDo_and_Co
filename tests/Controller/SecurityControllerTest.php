<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function setUp():void{
        $this->client = static::createClient();
    }

    public function testLoginIsUp(): void
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $testUser = $userRepository->findOneByEmail();
        $urlGeneration = $this->client->getContainer()->get('router.default');
        $this->client->request(Request::METHOD_GET, $urlGeneration->generate('security_login'));
    }
}
