<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use \Doctrine\DBAL\Logging\Middleware;

class UserControllerTest extends WebTestCase
{
    public function setUp(): void
    {
        $this->client = static::createClient();

        $this->userRepository = $this->client->

        $this->user = $this->userRepository->findOneByEmail('bernard92@free.fr');

        $this->urlGenerator = $this->client->getContainer()->get('router.default');

        $this->client->loginUser($this->user);
    }

    public function testUserListIsUp(): void
    {

        $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_list'));

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
