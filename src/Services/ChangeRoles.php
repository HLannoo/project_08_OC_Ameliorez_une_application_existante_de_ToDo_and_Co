<?php

namespace App\Services;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;


class ChangeRoles
{
    private $manager;
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }


    public function upgradeGuest(User $user)
    {
        $user->setRoles(['ROLE_USER']);

        $this->manager->persist($user);
        $this->manager->flush();


    }

}
