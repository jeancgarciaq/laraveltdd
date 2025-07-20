<?php
// tests/Feature/TaskManagementTest.php
namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskManagementTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_cannot_update_another_users_task()
    {
        // 1. Arrange:
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $taskUser2 = Task::factory()->for($user2)->create();

        // 2. Act: Autenticar como user1 e intentar actualizar la tarea de user2.
        // ¡Esta línea es clave y debe estar aquí!
        $response = $this->actingAs($user1)->put(route('tasks.update', $taskUser2), [
            'title'         => 'Attempted update by other user',
            'description'   => 'This should fail',
        ]);

        // 3. Assert: Esperar un error de autorización (403 Forbidden).
        $response->assertStatus(403);

        // 4. Asegurarse de que la tarea de user2 no fue modificada en la BD.
        $this->assertDatabaseHas('tasks', [
            'id'            => $taskUser2->id,
            'title'         => $taskUser2->title,
            'description'   => $taskUser2->description,
        ]);
    }
}