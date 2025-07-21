<?php

namespace App\Http\Controllers;

use App\Contracts\TaskRepositoryInterface;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    /**
     * @atribute @protected 
     */
    protected TaskRepositoryInterface $taskRepository;

    /**
     * Constructor del controlador
     *
     * Configura la autorización automática de recursos usando políticas.
     * Esto asegura que cada acción sea autorizada automáticamente.
     *
     * @return void
     */
    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
        $this->authorizeResource(Task::class, 'task');
    }

    /**
     * Muestra la lista de tareas del usuario autenticado
     *
     * Obtiene todas las tareas que pertenecen al usuario actual
     * y las muestra en la vista de índice.
     *
     * @return View Vista con la lista de tareas del usuario
     * 
     * @example
     * // El usuario ve solo sus tareas
     * GET /tasks
     */
    public function index()
    {
        // Establecer el usuario para el repositorio antes de cualquier operación
        $tasks = $this->taskRepository->forUser(auth()->user())->getAllTasks();

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Muestra el formulario para crear una nueva tarea
     *
     * @return View Vista del formulario de creación
     * 
     * @example
     * GET /tasks/create
     */
    public function create()
    {
        // authorizeResource() automatically calls create() policy method
        return view('tasks.create');
    }

    /**
     * Almacena una nueva tarea en la base de datos
     *
     * Crea una nueva tarea asociada al usuario autenticado
     * después de validar los datos del formulario.
     *
     * @param StoreTaskRequest $request Datos validados del formulario
     * @return RedirectResponse Redirección al índice con mensaje de éxito
     * 
     * @throws \Illuminate\Auth\Access\AuthorizationException Si el usuario no tiene permisos
     * 
     * @example
     * POST /tasks
     * {
     *     "title": "Nueva tarea",
     *     "description": "Descripción de la tarea"
     * }
     */
    public function store(StoreTaskRequest $request)
    {
        
        // Establecer el usuario y luego crear la tarea
        $this->taskRepository->forUser($request->user())->createTask($request->validated());

        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    /**
     * Muestra una tarea específica
     *
     * @param Task $task La tarea a mostrar (inyección de modelo)
     * @return View Vista con los detalles de la tarea
     * 
     * @throws \Illuminate\Auth\Access\AuthorizationException Si la tarea no pertenece al usuario
     * 
     * @example
     * GET /tasks/1
     */
    public function show(Task $task)
    {
        // Asegura que solo se busca la tarea del usuario actual
        $task = $this->taskRepository->forUser(auth()->user())->findTaskById($id);
        
        if (!$task) {
            abort(404);
        }
        
        return view('tasks.show', compact('task'));
    }

    /**
     * Muestra el formulario para editar una tarea existente
     *
     * @param Task $task La tarea a editar (inyección de modelo)
     * @return View Vista del formulario de edición
     * 
     * @throws \Illuminate\Auth\Access\AuthorizationException Si la tarea no pertenece al usuario
     * 
     * @example
     * GET /tasks/1/edit
     */
    public function edit(Task $task)
    {
        try {
            // Aunque la política ya está en el repositorio, la llamamos para el formulario de edición
            // o para asegurar que la tarea accedida a través de la inyección de modelo pertenece al usuario.
            // Una forma es intentar una operacióndummy o un método específico de autorización en el repo.
            // Para simplificar, si la Policy se usa en el repositorio, podrías depender de ella.
            // Si el findTaskById() ya es scoped, esta llamada es menos crítica.
            if ($task->user_id !== auth()->id()) { // Comprobación explícita para la vista
                 throw new AuthorizationException('You are not authorized to view this task.');
            }
        } catch (AuthorizationException $e) {
            abort(403, $e->getMessage());
        }
        
        return view('tasks.edit', compact('task'));
    }

    /**
     * Actualiza una tarea existente en la base de datos
     *
     * Modifica los datos de una tarea existente después de
     * validar los nuevos datos del formulario.
     *
     * @param UpdateTaskRequest $request Datos validados del formulario
     * @param Task $task La tarea a actualizar (inyección de modelo)
     * @return RedirectResponse Redirección al índice con mensaje de éxito
     * 
     * @throws \Illuminate\Auth\Access\AuthorizationException Si la tarea no pertenece al usuario
     * 
     * @example
     * PUT /tasks/1
     * {
     *     "title": "Título actualizado",
     *     "description": "Nueva descripción"
     * }
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        try {
            // El repositorio ya tiene el usuario a través de forUser() si se usara globalmente,
            // pero para update/delete sigue siendo mejor pasar el modelo completo
            // para que la política pueda operar con él. La Policy sigue siendo la fuente de la verdad.
            $this->taskRepository->updateTask(auth()->user(), $task, $request->validated()); // Seguir pasando el user para la policy en update/delete
            return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
        } catch (AuthorizationException $e) {
            abort(403, $e->getMessage());
        }
    }

    /**
     * Elimina una tarea de la base de datos
     *
     * Borra permanentemente una tarea del sistema.
     * Esta acción no se puede deshacer.
     *
     * @param Task $task La tarea a eliminar (inyección de modelo)
     * @return RedirectResponse Redirección al índice con mensaje de éxito
     * 
     * @throws \Illuminate\Auth\Access\AuthorizationException Si la tarea no pertenece al usuario
     * 
     * @example
     * DELETE /tasks/1
     */
    public function destroy(Task $task)
    {
        try {
            $this->taskRepository->deleteTask(auth()->user(), $task); // Seguir pasando el user para la policy en update/delete
            return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
        } catch (AuthorizationException $e) {
            abort(403, $e->getMessage());
        }
    }
}