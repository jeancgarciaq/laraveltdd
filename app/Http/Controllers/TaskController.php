<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Task::class, 'task');
    }

    public function index()
    {
        // authorizeResource() automatically calls viewAny() policy method
        $tasks = Auth::user()->tasks()->get();
        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        // authorizeResource() automatically calls create() policy method
        return view('tasks.create');
    }

    public function store(StoreTaskRequest $request)
    {
        // authorizeResource() automatically calls create() policy method
        $task = $request->user()->tasks()->create($request->validated());
        return redirect()->route('tasks.index')->with('success', 'Task created successfully!');
    }

    public function show(Task $task)
    {
        // authorizeResource() automatically calls view() policy method
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        // authorizeResource() automatically calls update() policy method
        return view('tasks.edit', compact('task'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        // authorizeResource() automatically calls update() policy method
        $task->update($request->only(['title', 'description']));
        return redirect()->route('tasks.index')->with('success', 'Task updated successfully!');
    }

    public function destroy(Task $task)
    {
        // authorizeResource() automatically calls delete() policy method
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully!');
    }
}