<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
</head>
<body>
    <h1>Edit Task: {{ $task->title }}</h1>

    <form method="POST" action="{{ route('tasks.update', $task) }}">
        @csrf
        @method('PUT') <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" value="{{ old('title', $task->title) }}"><br>
        @error('title')
            <div style="color: red;">{{ $message }}</div>
        @enderror

        <label for="description">Description:</label><br>
        <textarea id="description" name="description">{{ old('description', $task->description) }}</textarea><br>
        @error('description')
            <div style="color: red;">{{ $message }}</div>
        @enderror

        <button type="submit">Update Task</button>
    </form>

    <a href="{{ route('tasks.index') }}">Back to Tasks</a>
</body>
</html>