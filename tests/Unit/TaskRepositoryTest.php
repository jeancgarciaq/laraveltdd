<?php

namespace Tests\Unit;

use App\Contracts\TaskRepositoryInterface;
use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection; 
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Mockery;

class TaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected TaskRepositoryInterface $taskRepository;
    protected $gateMock;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock the Gate contract to control authorization behavior
        $this->gateMock = Mockery::mock(\Illuminate\Contracts\Auth\Access\Gate::class);
        // Bind our repository with the mocked Gate
        $this->app->instance(\Illuminate\Contracts\Auth\Access\Gate::class, $this->gateMock);
        $this->taskRepository = $this->app->make(TaskRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_can_set_the_user_context_for_operations()
    {
        $user = User::factory()->create();
        $repoInstance = $this->taskRepository->forUser($user);

        $this->assertInstanceOf(TaskRepositoryInterface::class, $repoInstance);
        // Aunque no podemos acceder directamente a $currentUser,
        // podemos verificar que las operaciones posteriores lo usan.
    }

    #[Test]
    public function it_retrieves_only_tasks_for_the_current_user_context()
    {
        // Arrange: Crear dos usuarios y tareas para cada uno
        $user1 = User::factory()->create();
        $task1_user1 = Task::factory()->for($user1)->create();
        $task2_user1 = Task::factory()->for($user1)->create();

        $user2 = User::factory()->create();
        $task_user2 = Task::factory()->for($user2)->create();

        // Act: Establecer el contexto para user1 y obtener tareas
        $tasks = $this->taskRepository->forUser($user1)->getAllTasks();

        // Assert: Solo deben aparecer las tareas de user1
        $this->assertCount(2, $tasks);
        $this->assertTrue($tasks->contains($task1_user1));
        $this->assertTrue($tasks->contains($task2_user1));
        $this->assertFalse($tasks->contains($task_user2));
    }

    #[Test]
    public function it_can_find_a_task_by_id_only_if_it_belongs_to_the_current_user()
    {
        // Arrange: Crear usuarios y tareas
        $user1 = User::factory()->create();
        $taskOfUser1 = Task::factory()->for($user1)->create();
        $user2 = User::factory()->create();
        $taskOfUser2 = Task::factory()->for($user2)->create();

        // Act & Assert 1: Buscar tarea de user1 con contexto de user1 (debe encontrarla)
        $foundTask = $this->taskRepository->forUser($user1)->findTaskById($taskOfUser1->id);
        $this->assertNotNull($foundTask);
        $this->assertTrue($foundTask->is($taskOfUser1));

        // Act & Assert 2: Buscar tarea de user2 con contexto de user1 (NO debe encontrarla)
        $foundTask = $this->taskRepository->forUser($user1)->findTaskById($taskOfUser2->id);
        $this->assertNull($foundTask); // Debe devolver null porque no pertenece a user1
    }

    #[Test]
    public function it_creates_a_task_for_the_current_user_context()
    {
        $user = User::factory()->create();
        $data = ['title' => 'Contextual Task', 'description' => 'Created in user context'];

        $task = $this->taskRepository->forUser($user)->createTask($data);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('Contextual Task', $task->title);
        $this->assertEquals($user->id, $task->user_id);
        $this->assertDatabaseHas('tasks', ['title' => 'Contextual Task', 'user_id' => $user->id]);
    }

     #[Test]
    public function it_can_create_a_task_for_a_user()
    {
        $user = User::factory()->create();
        $data = ['title' => 'New Task', 'description' => 'A description'];

        // Adaptación: Primero establece el contexto de usuario, luego llama a createTask
        $task = $this->taskRepository->forUser($user)->createTask($data);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals('New Task', $task->title);
        $this->assertEquals($user->id, $task->user_id);
        $this->assertDatabaseHas('tasks', ['title' => 'New Task', 'user_id' => $user->id]);
    }

    #[Test]
    public function it_can_update_a_task_if_authorized()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create(['title' => 'Old Title']);
        $newData = ['title' => 'Updated Title'];

        // Expect the gate to allow the update for the given user and task
        $this->gateMock->shouldReceive('denies')
                        ->once()
                        ->with('update', Mockery::on(function ($arg) use ($task) {
                            return $arg->is($task);
                        }))
                        ->andReturn(false); // Does NOT deny access

        // Adaptación: Primero establece el contexto de usuario, luego llama a updateTask
        $result = $this->taskRepository->forUser($user)->updateTask($task, $newData);

        $this->assertTrue($result);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Updated Title']);
    }

    #[Test]
    public function it_throws_authorization_exception_when_updating_unauthorized_task()
    {
        $this->expectException(AuthorizationException::class);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($otherUser)->create(['title' => 'Old Title']);
        $newData = ['title' => 'Updated Title'];

        // Expect the gate to deny the update
        $this->gateMock->shouldReceive('denies')
                        ->once()
                        ->with('update', Mockery::on(function ($arg) use ($task) {
                            return $arg->is($task);
                        }))
                        ->andReturn(true); // DENIES access

        // Adaptación: Primero establece el contexto de usuario, luego llama a updateTask
        $this->taskRepository->forUser($user)->updateTask($task, $newData);

        // Assert that the task was NOT updated (this part will not be reached if exception is thrown)
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Old Title']);
    }

    #[Test]
    public function it_can_delete_a_task_if_authorized()
    {
        $user = User::factory()->create();
        $task = Task::factory()->for($user)->create();

        $this->gateMock->shouldReceive('denies')
                        ->once()
                        ->with('delete', Mockery::on(function ($arg) use ($task) {
                            return $arg->is($task);
                        }))
                        ->andReturn(false);

        // Adaptación: Primero establece el contexto de usuario, luego llama a deleteTask
        $result = $this->taskRepository->forUser($user)->deleteTask($task);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    #[Test]
    public function it_throws_authorization_exception_when_deleting_unauthorized_task()
    {
        $this->expectException(AuthorizationException::class);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->for($otherUser)->create();

        $this->gateMock->shouldReceive('denies')
                        ->once()
                        ->with('delete', Mockery::on(function ($arg) use ($task) {
                            return $arg->is($task);
                        }))
                        ->andReturn(true);

        // Adaptación: Primero establece el contexto de usuario, luego llama a deleteTask
        $this->taskRepository->forUser($user)->deleteTask($task);

        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }
}