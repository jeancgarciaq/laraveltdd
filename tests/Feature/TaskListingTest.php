<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;

class TaskListingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_only_see_their_own_tasks_on_the_tasks_page()
    {
        //1. Arrange: Crear dos usuarios y tareas para cada uno
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task1_user1 = Task::factory()->for($user1)->create(['title' => 'Task 1 for user1', 'description' => 'This the first tasks by user1']);
        $task2_user1 = Task::factory()->for($user1)->create(['title' => 'Task 2 for user1', 'description' => 'This the second tasks by user1']);
        $task1_user2 = Task::factory()->for($user2)->create(['title' => 'Task for user2', 'description' => 'This the first tasks by user2']);

        //2. Act: Autenticarse como user1 y acceder a la página de tareas
        $this->actingAs($user1);
        $response = $this->get('/tasks');

        //3. Assert:
        // La página carga correctamente
        $response->assertOk();

        //Asegurarse de que las tareas de user1 son visibles
        $response->assertSee($task1_user1->title);
        $response->assertSee($task2_user1->title);

        //Asegurarse de que la tarea de user2 No es visible
        $response->assertDontSee($task1_user2->title);
    }

    #[Test]
    public function guests_cannot_view_tasks_page() 
    {
        //1. Crear lista de tareqas
        Task::factory(3)->create();

        //2. Un invitado no debería poder ver la página de tareas
        $response = $this->get('/tasks');

        //3. Debe redirigir al login
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}
