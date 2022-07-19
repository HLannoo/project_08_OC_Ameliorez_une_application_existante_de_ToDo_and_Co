<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ToDoFixtures extends Fixture
{
    public function __construct(protected UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');


        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                "password"
            );
            $user->setEmail($faker->email)
                ->setUsername($faker->firstName)
                ->setRoles(["ROLE_USER"])
                ->setPassword($hashedPassword)
                ->setIsVerified(1);

            $manager->persist($user);

            for ($a = 0; $a < 5; $a++) {
                $task = new Task();
                $task->setTitle($faker->text(15))
                    ->setContent($faker->paragraph(1))
                    ->setCurrentUser($user)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setIsDone(false);

                $manager->persist($task);
                }
        }

        for ($u = 0; $u < 1; $u++) {
            $admin = new User();
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                "password"
            );
            $admin->setUserName($faker->firstName)
                ->setEmail($faker->email)
                ->setPassword($hashedPassword)
                ->setRoles(["ROLE_ADMIN"])
                ->setIsVerified(1);

            $manager->persist($admin);

            $anonyme = new User();
            $anonyme->setUserName("Anonyme")
                ->setEmail($faker->email)
                ->setPassword($hashedPassword)
                ->setRoles(["ROLE_USER"])
                ->setIsVerified(1);

            $manager->persist($anonyme);

            for ($a = 0; $a < 5; $a++) {
                $task = new Task();
                $task->setTitle($faker->text(15))
                    ->setContent($faker->paragraph(1))
                    ->setCurrentUser($anonyme)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setIsDone(false);
                for ($a = 0; $a < 5; $a++) {
                    $task = new Task();
                    $task->setTitle($faker->text(15))
                        ->setContent($faker->paragraph(1))
                        ->setCurrentUser($admin)
                        ->setCreatedAt(new \DateTimeImmutable())
                        ->setIsDone(false);

                    $manager->persist($task);
                }
            }
        }

        $manager->flush();
    }
}
