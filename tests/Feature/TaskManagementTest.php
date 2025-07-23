<?php

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
    public function a_user_can_delete_their_own_task_and_is_redirected()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);

        // El controlador usará authorizeResource() que a su vez usa la política.
        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success', 'Task deleted successfully!');
    }

    #[Test]
    public function a_task_update_requires_a_title()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create([
            'title' => 'Initial Title',
            'description' => 'Initial Description',
        ]);

        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => '',
            'description' => 'Updated Description',
        ]);

        $response->assertSessionHasErrors('title');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Initial Title',
        ]);
        $this->assertDatabaseMissing('tasks', ['title' => '']);
    }

    #[Test]
    public function a_user_cannot_update_another_users_task()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $taskOfUser1 = Task::factory()->for($user1)->create();
        $taskOfUser2 = Task::factory()->for($user2)->create();

        $response = $this->actingAs($user1)->put(route('tasks.update', $taskOfUser2), [
            'title' => 'Attempted update by other user',
            'description' => 'This should fail',
        ]);

        $response->assertStatus(403); // ✅ Fixed

        $this->assertDatabaseHas('tasks', [
            'id' => $taskOfUser2->id,
            'title' => $taskOfUser2->title,
        ]);
        $this->assertDatabaseMissing('tasks', ['title' => 'Attempted update by other user']);
    }

    #[Test]
    public function a_user_cannot_delete_another_users_task()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $taskOfUser2 = Task::factory()->for($user2)->create();

        $response = $this->actingAs($user1)->delete(route('tasks.destroy', $taskOfUser2));

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $taskOfUser2->id]);
    }


    #[Test]
    public function an_authenticated_user_can_view_the_edit_task_page_for_their_own_task()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $response = $this->actingAs($user)->get(route('tasks.edit', $task));

        $response->assertOk(); // Debería cargar la página
        $response->assertSee('Edit Task'); // Verificar que el título de la página está presente
        $response->assertSee($task->title); // Verificar que el título de la tarea está en el formulario
    }

    #[Test]
    public function a_user_cannot_view_the_edit_task_page_for_another_users_task()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $taskOfUser2 = Task::factory()->for($user2)->create();

        $response = $this->actingAs($user1)->get(route('tasks.edit', $taskOfUser2));

        $response->assertForbidden(); // Debe denegar el acceso (403)
    }

    #[Test]
    public function an_authenticated_user_can_view_the_create_task_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('tasks.create'));

        $response->assertOk(); // HTTP 200 OK
        $response->assertSee('Create New Task'); // Verificar título de la página
        $response->assertSeeHtml('<form method="POST" action="' . route('tasks.store') . '">'); // Verificar que el formulario existe y apunta a la ruta correcta
        $response->assertSee('<input type="text" id="title" name="title"', false); // Verificar campo de título
        $response->assertSee('<textarea id="description" name="description"', false); // Verificar campo de descripción
        $response->assertSee('<input type="hidden" name="_token"', false); // Verificar el token CSRF
    }

    #[Test]
    public function guests_cannot_view_the_create_task_page()
    {
        $response = $this->get(route('tasks.create'));

        $response->assertRedirect('/login'); // Redirigir al login
        $response->assertStatus(302);
    }

    #[Test]
    public function form_displays_validation_errors_for_missing_title()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('tasks.create'));

        $response = $this->post(route('tasks.store'), [
            'title' => '',
            'description' => 'Some description',
        ]);

        $response->assertSessionHasErrors('title');
        $response->assertStatus(302);

        $response = $this->followRedirects($response);

        $response->assertSee('The title field is required.');
        $response->assertSeeHtml('<span class="error-message">The title field is required.</span>');
        $response->assertSee('value="Some description"', false);
        $response->assertSeeHtml('<textarea id="description" name="description">Some description</textarea>');

        $this->assertDatabaseCount('tasks', 0);
    }

    #[Test]
    public function form_displays_validation_errors_for_too_long_title()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('tasks.create'));

        $longTitle = str_repeat('a', 256);
        $response = $this->post(route('tasks.store'), [
            'title' => $longTitle,
            'description' => 'A valid description',
        ]);

        $response->assertSessionHasErrors('title');
        $response->assertStatus(302);

        $response = $this->followRedirects($response);

        $response->assertSee('The title field must not be greater than 255 characters.');
        $response->assertSeeHtml('<span class="error-message">The title field must not be greater than 255 characters.</span>');
        $response->assertSee('value="' . $longTitle . '"', false);
        $response->assertSeeHtml('<textarea id="description" name="description">A valid description</textarea>');

        $this->assertDatabaseCount('tasks', 0);
    }
}