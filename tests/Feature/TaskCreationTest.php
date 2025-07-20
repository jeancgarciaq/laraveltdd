<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;

class TaskCreationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_user_can_create_a_task()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'A new task from a user',
            'description' => 'This is the description for the new task by specific user.',
        ]);

        $response->assertSessionHasNoErrors();
        
        $this->assertDatabaseHas('tasks', [
            'title' => 'A new task from a user',
            'user_id' => $user->id,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success', 'Task created successfully!');
    }

    #[Test]
    public function guests_cannot_create_tasks()
    {
        $response = $this->post(route('tasks.store'), [
            'title' => 'Guest task',
            'description' => 'Should not be created,',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('tasks', ['title' => 'Guest task']);
    }

    #[Test]
    public function a_task_requires_a_title()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => '',
            'description' => 'Description without title.',
        ]);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseMissing('tasks', ['description' => 'Description without title.']);
        $this->assertCount(0, Task::all());
    }

    #[Test]
    public function a_task_description_is_optional()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Task without description',
            'description' => '',
        ]);

        // Description is optional, so this should succeed
        $response->assertSessionHasNoErrors();
        
        // ✅ Fix: Expect null instead of empty string
        $this->assertDatabaseHas('tasks', [
            'title' => 'Task without description',
            'description' => null, // Changed from '' to null
            'user_id' => $user->id,
        ]);
        
        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success', 'Task created successfully!');
    }

    #[Test]
    public function a_task_can_have_empty_description_alternative_check()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'title' => 'Task with empty description',
            'description' => '',
        ]);

        $response->assertSessionHasNoErrors();
        
        // Alternative way: Just check the task exists and description is nullable
        $task = Task::where('title', 'Task with empty description')->first();
        $this->assertNotNull($task);
        $this->assertEmpty($task->description); // This works for both null and empty string
        $this->assertEquals($user->id, $task->user_id);
    }
}