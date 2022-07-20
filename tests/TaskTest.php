<?php

namespace App\Tests;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGetId()
    {
        try {
            $task = new Task();
            $this->assertEquals(null, $task->getId());
        }
        catch(\Exception $exception){
            $this->assertStringContainsString('Le champ est différent de null', $exception->getMessage());
        }
    }
}
