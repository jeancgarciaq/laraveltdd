<?php

namespace App\Http\Controllers;

// Importa el modelo
use App\Models\Task; 
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(Request $request)
    {
        // Validación básica para que la prueba pase
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
        ]);

        // Crear la tarea
        Task::create($request->all());

        // Redirigir para que la prueba pase
        return redirect('/tasks');
    }

    public function index()
    {
        // Por ahora, solo una vista vacía para que la redirección funcione
        return view('tasks.index');
    }
}