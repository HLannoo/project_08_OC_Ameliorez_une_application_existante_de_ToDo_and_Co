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
        $this->passwordHasher = $this->passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');


        for ($i = 0; $i < 10; $i++) {
            $user = new User;
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
                $task = new Task;
                $task->setTitle($faker->text(15))
                    ->setContent($faker->paragraph(1))
                    ->setCurrentUser($user)
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setIsDone(false);

                $manager->persist($task);
            }
            $manager->flush();
        }

        for ($u = 0; $u < 1; $u++) {
            $adminTest = new User;
            $hashedPassword = $this->passwordHasher->hashPassword(
                $adminTest,
                "password"
            );
            $adminTest->setUserName("Admin")
                ->setEmail("admin-test@gmail.com")
                ->setPassword($hashedPassword)
                ->setRoles(["ROLE_ADMIN"])
                ->setIsVerified(1);

            $manager->persist($adminTest);

            $anonymeTest = new User;
            $anonymeTest->setUserName("Anonyme")
                ->setEmail("anonymous-test@gmail.com")
                ->setPassword($hashedPassword)
                ->setRoles(["ROLE_USER"])
                ->setIsVerified(1);

            $manager->persist($anonymeTest);

            $userTest = new User;
            $userTest->setUserName("User")
                ->setEmail("user-test@gmail.com")
                ->setPassword($hashedPassword)
                ->setRoles(["ROLE_USER"])
                ->setIsVerified(1);

            $manager->persist($userTest);

        }
        for ($k = 0; $k < 10; $k ++)
        {
            $anonymeTestTask = new Task;
            $anonymeTestTask->setTitle("TestTaskAnonymous$k")
                ->setContent($faker->paragraph(1))
                ->setCurrentUser($anonymeTest)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setIsDone(false);

            $manager->persist($anonymeTestTask);
        }


        for ($a = 0; $a < 5; $a++) {
            $anonymeTask = new Task;
            $anonymeTask->setTitle($faker->text(15))
                ->setContent($faker->paragraph(1))
                ->setCurrentUser($anonymeTest)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setIsDone(false);

            $manager->persist($anonymeTask);

            $adminTask = new Task;
            $adminTask->setTitle($faker->text(15))
                ->setContent($faker->paragraph(1))
                ->setCurrentUser($adminTest)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setIsDone(false);

            $manager->persist($adminTask);

            $userTask = new Task;
            $userTask->setTitle($faker->text(15))
                ->setContent($faker->paragraph(1))
                ->setCurrentUser($userTest)
                ->setCreatedAt(new \DateTimeImmutable())
                ->setIsDone(false);

            $manager->persist($userTask);

            $manager->flush();
        }

    }
}
