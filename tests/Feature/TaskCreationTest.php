<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;

class TaskCreationTest extends TestCase
{
    //Esto resetea la base de datos para cada prueba
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_task()
    {
        // 1. Simular que un usuario crea una tarea
        // Aquí simulamos una petición POST a la ruta /tasks
        $response = $this->post('/tasks', [
            'title'         => 'A new task',
            'description'   => 'This is the description for the new task.',
        ]);

        // 2. Verificar que la tarea fue almacenada en la base de datos
        $this->assertDatabaseHas('tasks', [
            'title' => 'A new task',
        ]);

        // 3. Verificar que la redirección o respuesta es correcta
        // Por ahora, solo nos aseguraremos de que la partición fue exitosa (código 200 o 302 para redirección)
        // Esperamos una redirección después de crear
        $response->assertStatus(302);
        // Y que redirija a /tasks
        $response->assertRedirect('/tasks');
    }

    /** @test */
    public function a_user_can_view_all_tasks()
    {
        // 1. Crear algunas tareas en la base de datos
        // Usamos el factory de Laravel para crear datos de prueba fácilmente
        Task::factory()->create(['title' => 'First Task', 'description' => 'Description 1']);
        Task::factory()->create(['title' => 'Second Task', 'description' => 'Description 2']);

        // 2. Simular una petición GET a la rutas /tasks
        $response = $this->get('/tasks');

        // 3. Verificar que la respuesta contiene las tareas
        // Verificar que la petición fue exitosa (código 200)
        $response->assertOk();
        $response->assertSee('First Task');
        $response->assertSee('Second Task');
    }
}
