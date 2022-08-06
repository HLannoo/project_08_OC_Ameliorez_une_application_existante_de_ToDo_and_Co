<?php

namespace App\Tests\Services;

use App\Entity\User;

use App\Repository\UserRepository;
use App\Services\ChangeRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChangeRolesTest extends WebTestCase
{

    private $changeRole;
    private $em;
    private $client;
    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->changeRole = static::getContainer()->get(ChangeRoles::class);
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $this->container = $this->client->getContainer()->get(UserRepository::class);


    }


    public function testChangeRolesServices(): void
    {
        $user = new User;
        $user->setEmail("crtest@gmail.com")
            ->setUsername("crtest")
            ->setPassword("crpassword");
        $this->em->persist($user);
        $this->em->flush();

        $this->assertEmpty($user->getRoles());
        $this->changeRole->upgradeGuest($user);
        $this->assertTrue($user->getRoles() === ['ROLE_USER']);

    }
}
