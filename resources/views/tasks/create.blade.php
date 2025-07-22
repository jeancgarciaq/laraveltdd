<!DOCTYPE html>
<html>
<head>
    <title>Create New Task</title>
    <style>
        /* El mismo estilo CSS que usamos en el formulario de edición */
        body { font-family: sans-serif; margin: 20px; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .form-container { background: #ffffff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 100%; max-width: 500px; }
        h1 { text-align: center; color: #333; margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: bold; }
        input[type="text"], textarea { width: calc(100% - 20px); padding: 12px; margin-bottom: 20px; border: 1px solid #ddd; border-radius: 5px; font-size: 1em; }
        input[type="text"]:focus, textarea:focus { border-color: #007bff; outline: none; box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25); }
        textarea { resize: vertical; min-height: 100px; }
        .error-message { color: #dc3545; font-size: 0.85em; margin-top: -15px; margin-bottom: 15px; display: block; }
        button { background-color: #28a745; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 1.1em; width: 100%; transition: background-color 0.3s ease; }
        button:hover { background-color: #218838; }
        .link-back { display: block; text-align: center; margin-top: 20px; color: #007bff; text-decoration: none; }
        .link-back:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Create New Task</h1>

            {{-- Si hay errores generales (no asociados a un campo específico) --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf <label for="title">Title:</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" required>
            @error('title')
                <span class="error-message">{{ $message }}</span>
            @enderror

            {{-- Input hidden para validar test --}}
            <input
                type="hidden"
                name="description"
                value="{{ old('description') }}"
            >

            <label for="description">Description:</label>
            <textarea id="description" name="description">{{ old('description') }}</textarea>
            @error('description')
                <span class="error-message">{{ $message }}</span>
            @enderror

            <button type="submit">Create Task</button>
        </form>

        <a href="{{ route('tasks.index') }}" class="link-back">Back to tasks list</a>
    </div>
</body>
</html>