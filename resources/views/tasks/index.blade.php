<!DOCTYPE html>
<html>
<head>
    <title>Tasks</title>
</head>
<body>
    <h1>All Tasks</h1>

    <ul>
        @foreach ($tasks as $task)
            <li>
                <strong>{{ $task->title }}</strong>
                <p>{{ $task->description }}</p>
            </li>
        @endforeach
    </ul>
    </body>
</html>