<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

use Tests\TestCase;

class UserRelationshipTest extends TestCase
{
    // TO ensure a clean database for each test
    use RefreshDatabase;

    #[Test]
    public function a_user_can_have_many_tasks()
    {
        // 1. Arrange: Crear un usuario y algunas tareas asociadas a él.
        $user = User::factory()->create();
        $task1 = Task::factory()->for($user)->create(['title' => 'User Task 1', 'description' => 'A new description for Task 1']);    
        $task2 = Task::factory()->for($user)->create(['title' => 'User Task 2', 'description' => 'A new description for Task 2']);

        //2. Act; Acceder a la ŕelación de tareas del usuario
        // Accedemos con la colección de tareas
        $tasks = $user->tasks;

        // 3. Assert: Verificar que es una colección y que contiene las tareas correctas
        $this->assertInstanceOf(Collection::class, $tasks);
        $this->assertCount(2, $tasks);
        $this->assertTrue($tasks->contains($task1));
        $this->assertTrue($tasks->contains($task2));
        $this->assertEquals('User Task 1', $tasks->first()->title);
    }

    #[Test]
    public function deleting_a_user_also_deletes_their_tasks()
    {
        //1. Arrange: Crear un usuario y algunas tareas para él
        $user = User::factory()->create();
        $task1 = Task::factory()->for($user)->create(['title' => 'Taks 1', 'description' => 'Description 1']);
        $task2 = Task::factory()->for($user)->create(['title' => 'Taks 2', 'description' => 'Description 2']);

        //Verificar que las tareas existen en la base de datos inicialmente
        $this->assertDatabaseHas('tasks', ['id' => $task1->id]);
        $this->assertDatabaseHas('tasks', ['id' => $task2->id]);

        //2. Act: Eliminar el usuario
        $user->delete();

        //3. Assert:
        // Verificar que el usuario ya no está en la base de datos
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        //Verificar que las tareas asociadas al usuario también han sido eliminadas.
        $this->assertDatabaseMissing('tasks', ['id' => $task1->id]);
        $this->assertDatabaseMissing('tasks', ['id' => $task2->id]);
    }
}
