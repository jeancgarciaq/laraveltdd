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
        // Por ahora, solo una vista vacía para que la redirección funcione
        return view('tasks.index');
    }
}