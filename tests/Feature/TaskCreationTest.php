<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;


class TaskCreationTest extends TestCase
{
    //Esto resetea la base de datos para cada prueba
    use RefreshDatabase;

    #[Test]
    public function a_user_can_create_a_task()
    {
        //1. Arrange: Crear un usuario autenticado para que cree la tarea.
        // Simulamos la autenticación de un usuario con el helper `actingAs()`.
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. Act: Simular que un usuario crea una tarea
        // Aquí simulamos una petición POST a la ruta /tasks
        $response = $this->post('/tasks', [
            'title'         => 'A new task from a user',
            'description'   => 'This is the description for the new task by specific user.',
        ]);

        // 3. Assert:
        // Verificar la validación
        $response->assertSessionHasNoErrors();
        // Verificar que la tarea fue almacenada en la base de datos con el user_id correcto.
        $this->assertDatabaseHas('tasks', [
            'title' => 'A new task from a user',
            //Aquí verificamos la relación
            'user_id' => $user->id,
        ]);


        // 4. Verificar que la redirección o respuesta es correcta
        // Por ahora, solo nos aseguraremos de que la partición fue exitosa (código 200 o 302 para redirección)
        // Esperamos una redirección después de crear
        $response->assertStatus(302);
        // Y que redirija a /tasks
        $response->assertRedirect('/tasks');
    }

    #[Test]
    public function guests_cannot_create_tasks()
    {
        //Si el usuario no está autenticado, no debería poder crear tareas
        $response = $this->post('/tasks', [
            'title' => 'Guest task',
            'description' => 'Should not be created,',
        ]);

        //Esperamos una redirección a login (código 200)
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        //Y verificamos que la tarea NO esté en la base de datos
        $this->assertDatabaseMissing('tasks', ['title' => 'Guest task']);
    }

}
