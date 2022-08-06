<?php

namespace App\Tests\Form;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testTaskForm(): void
    {
        $data = ['title' => 'testTitle', 'content' => 'testContent' ];

        $taskTest = New Task();
        $form = $this->factory->create(TaskType::class, $taskTest);
        $form->submit($data);


        $task = new Task;
        $task->setTitle('testTitle');
        $task->setContent('testContent');



        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());
        $this->assertEquals($task, $taskTest);
        $this->assertEquals($task->getTitle(), $taskTest->getTitle());
        $this->assertEquals($task->getContent(), $taskTest->getContent());
        $this->assertInstanceOf(Task::class, $form->getData());
    }
}
