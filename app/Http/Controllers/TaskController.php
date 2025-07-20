<?php

namespace App\Http\Controllers;

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
     * Constructor del controlador
     *
     * Configura la autorización automática de recursos usando políticas.
     * Esto asegura que cada acción sea autorizada automáticamente.
     *
     * @return void
     */
    public function __construct()
    {
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
        // authorizeResource() automatically calls viewAny() policy method
        $tasks = Auth::user()->tasks()->get();
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
        // authorizeResource() automatically calls create() policy method
        $task = $request->user()->tasks()->create($request->validated());
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
        // authorizeResource() automatically calls view() policy method
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
        // authorizeResource() automatically calls update() policy method
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
        // authorizeResource() automatically calls update() policy method
        $task->update($request->only(['title', 'description']));
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
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
        // authorizeResource() automatically calls delete() policy method
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }
}