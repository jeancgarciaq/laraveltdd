<?php

namespace App\Contracts;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException; // Importar la excepción
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function forUser(User $user): self; // Añade este método para establecer el usuario
    public function getAllTasks(): Collection; // Ahora no necesita el usuario como argumento
    public function createTask(array $data): Task; // Ahora no necesita el usuario como argumento
    public function findTaskById(int $id): ?Task;
    /** @throws AuthorizationException */
    public function updateTask(Task $task, array $data): bool;
    /** @throws AuthorizationException */
    public function deleteTask(Task $task): bool;
}