<?php

namespace App\Http\Controllers;

use App\Contracts\TaskRepositoryInterface;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException; // Importar la excepción

/**
 * Controlador para la gestión de tareas de usuarios
 *
 * Este controlador maneja todas las operaciones CRUD para las tareas,
 * incluyendo autorización automática a través de políticas.
 * Solo los usuarios autenticados pueden acceder a estas funcionalidades.
 *
 * @package App\Http\Controllers
 * @author Jean Carlo Garcia
 * @version 1.0
 * @since Laravel 12
 */
class TaskController extends Controller
{
    protected TaskRepositoryInterface $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
        // authorizeResource() es excelente, maneja la autorización para todos los métodos CRUD.
        // Mapea automáticamente:
        // index -> viewAny
        // create -> create
        // store -> create
        // show -> view
        // edit -> update
        // update -> update
        // destroy -> delete
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Muestra la lista de tareas del usuario autenticado
     */
    public function index()
    {
        // authorizeResource() ya llama a la política viewAny().
        // El repositorio ya está configurado para filtrar por el usuario actual.
        $tasks = $this->taskRepository->forUser(auth()->user())->getAllTasks();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Muestra el formulario para crear una nueva tarea
     */
    public function create()
    {
        // authorizeResource() ya llama automáticamente a la política create() para Task::class.
        // Si el usuario no tiene permiso para crear, authorizeResource() lanzará un 403.
        return view('tasks.create');
    }

    /**
     * Almacena una nueva tarea en la base de datos
     */
    public function store(StoreTaskRequest $request)
    {
        // authorizeResource() ya llama automáticamente a la política create() para Task::class.
        // Si la validación falla en StoreTaskRequest, Laravel redirige automáticamente.
        // Si la validación pasa, el repositorio crea la tarea para el usuario autenticado.
        $this->taskRepository->forUser($request->user())->createTask($request->validated());

        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    /**
     * Muestra una tarea específica
     */
    public function show(Task $task) // <-- $task se inyecta por Route Model Binding
    {
        // authorizeResource() ya llama a la política view() para esta $task.
        // Si el usuario no tiene permiso para verla, Laravel lanzará un 403.
        // NO necesitas volver a buscar la tarea con findTaskById ni usar $id.
        // La $task que recibes ya es el modelo correcto y ha pasado la autorización.

        return view('tasks.show', compact('task'));
    }

    /**
     * Muestra el formulario para editar una tarea existente
     */
    public function edit(Task $task) // <-- $task se inyecta por Route Model Binding
    {
        // authorizeResource() ya llama a la política update() para esta $task.
        // Si el usuario no tiene permiso para editarla, Laravel lanzará un 403.
        
        return view('tasks.edit', compact('task'));
    }

    /**
     * Actualiza una tarea existente en la base de datos
     */
    public function update(UpdateTaskRequest $request, Task $task) // <-- $task se inyecta por Route Model Binding
    {
        // authorizeResource() ya llama a la política update() para esta $task.
        // Si la validación falla en UpdateTaskRequest, Laravel redirige automáticamente.
        // Si la validación pasa, el repositorio actualiza la tarea.

        $this->taskRepository->updateTask($task, $request->validated());

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }

    /**
     * Elimina una tarea de la base de datos
     */
    public function destroy(Task $task) // <-- $task se inyecta por Route Model Binding
    {
        // authorizeResource() ya llama a la política delete() para esta $task.
        // Si el usuario no tiene permiso para eliminarla, Laravel lanzará un 403.

        $this->taskRepository->deleteTask($task);
        
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }
}