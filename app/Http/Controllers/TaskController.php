<?php

namespace App\Http\Controllers;

// Importa el modelo
use App\Models\Task; 
use Illuminate\Http\Request;
use App\Http\Requests\StoreTaskRequest;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request)
    {
        // Usa validated() para obtener solo los datos validados
        Task::create($request->validated());

        // Redirigir para que la prueba pase
        return redirect()->route('tasks.index');
    }

    public function index()
    {
        // Obtener todas las tareas
        $tasks = Task::all();
        // Pasar las tareas a la vista
        return view('tasks.index', compact('tasks'));
    }
}