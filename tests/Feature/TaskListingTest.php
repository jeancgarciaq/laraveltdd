<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $task1_user1 = Task::factory()->for($user1)->create([
            'title' => 'Task 1 for user1',
            'description' => 'This the first tasks by user1'
        ]);
        $task2_user1 = Task::factory()->for($user1)->create([
            'title' => 'Task 2 for user1',
            'description' => 'This the second tasks by user1'
        ]);
        $task1_user2 = Task::factory()->for($user2)->create([
            'title' => 'Task for user2',
            'description' => 'This the first tasks by user2'
        ]);

        $response = $this->actingAs($user1)->get(route('tasks.index')); // ✅ Use route names

        $response->assertOk();
        $response->assertViewIs('tasks.index'); // ✅ Additional assertion
        $response->assertViewHas('tasks'); // ✅ Additional assertion

        $response->assertSee($task1_user1->title);
        $response->assertSee($task2_user1->title);
        $response->assertDontSee($task1_user2->title);
    }

    #[Test]
    public function guests_cannot_view_tasks_page() 
    {
        Task::factory(3)->create();

        $response = $this->get(route('tasks.index')); // ✅ Use route names

        $response->assertRedirect(route('login')); // ✅ Use route names
    }

    #[Test]
    public function user_with_no_tasks_sees_empty_page() // ✅ Additional test
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('tasks.index'));

        $response->assertOk();
        $response->assertViewHas('tasks');
        // Verify the tasks collection is empty
        $this->assertCount(0, $response->viewData('tasks'));
    }
}