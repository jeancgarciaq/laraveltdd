<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test; 

class TaskRelationshipTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_task_belongs_to_a_user()
    {
        //1. Arrange: Crear un usuario y una tarea asociada a ese usuario
        $user = User::factory()->create();
        //El helper 'for' es excelente para relaciones BelongsTo
        $task = Task::factory()->for($user)->create();

        //2. Act: Acceder a la relación de usuario de la tarea
        //Accedemos al modelo del usuario
        $owner = $task->user;

        //3. Assert: Verificar que es una instancia del modelo User y que es el usuario correcto
        $this->assertInstanceOf(User::class, $owner);
        $this->assertEquals($user->id, $owner->id);
    }
}
