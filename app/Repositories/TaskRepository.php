<?php

namespace App\Repositories;

use App\Contracts\TaskRepositoryInterface;
use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Auth\Access\Gate; // Importa el contrato de Gate

class TaskRepository implements TaskRepositoryInterface
{
    protected Gate $gate;
    protected ?User $currentUser = null; // Almacena el usuario actual

    public function __construct(Gate $gate) // Inyecta la Gate
    {
        $this->gate = $gate;
    }

    public function forUser(User $user): self
    {
        $this->currentUser = $user;
        return $this; // Retorna la propia instancia para encadenamiento
    }

    protected function getAuthorizedUser(): User
    {
        if (is_null($this->currentUser)) {
            // Esto solo debería pasar si no se llamó forUser() y se intenta operar
            throw new \LogicException('User context not set for TaskRepository. Call forUser() first.');
        }
        return $this->currentUser;
    }

    public function getAllTasks(): Collection
    {
        return $this->getAuthorizedUser()->tasks()->get();
    }

    public function createTask(array $data): Task
    {
        return $this->getAuthorizedUser()->tasks()->create($data);
    }

    public function findTaskById(int $id): ?Task
    {
        // Esto automáticamente filtra por el usuario
        return $this->getAuthorizedUser()->tasks()->find($id);
    }

    public function updateTask(Task $task, array $data): bool
    {
        // La política sigue siendo importante para verificar la propiedad del objeto
        // ya que la tarea podría haber sido recuperada sin forUser().
        if ($this->gate->denies('update', $task)) {
            throw new AuthorizationException('You are not authorized to update this task.');
        }
        // Opcional: Asegurarse de que la tarea pertenece al usuario actual (redundante con policy)
        // if ($task->user_id !== $this->getAuthorizedUser()->id) {
        //     throw new AuthorizationException('This task does not belong to you.');
        // }
        return $task->update($data);
    }

    public function deleteTask(Task $task): bool
    {
        if ($this->gate->denies('delete', $task)) {
            throw new AuthorizationException('You are not authorized to delete this task.');
        }
        return $task->delete();
    }
}