<!DOCTYPE html>
<html>
<head>
    <title>Todo</title>
    <style>
        body {
            font-family: sans-serif;
            background: #eaf3f3;
            display: flex;
            justify-content: center;
            padding-top: 50px;
        }

        .container {
            width: 400px;
        }

        h1 {
            text-align: center;
        }

        .input-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        input {
            flex: 1;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        button {
            padding: 10px 15px;
            border-radius: 10px;
            border: none;
            background: pink;
            cursor: pointer;
        }

        .task {
            background: #fdf6e3;
            padding: 12px;
            border-radius: 15px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .done {
            text-decoration: line-through;
            background: #c8f7c5;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>TODO</h1>

    <form method="POST" action="/tasks" class="input-box">
        @csrf
        <input type="text" name="title" placeholder="Add a task..." required>
        <button>Add</button>
    </form>

    @foreach ($tasks as $task)
        <div class="task {{ $task->is_done ? 'done' : '' }}">
            <form method="POST" action="/tasks/{{ $task->id }}">
                @csrf
                @method('PATCH')
                <button type="submit">
                    {{ $task->is_done ? '✔' : '○' }}
                </button>
            </form>

            <span>{{ $task->title }}</span>

            <form method="POST" action="/tasks/{{ $task->id }}">
                @csrf
                @method('DELETE')
                <button>X</button>
            </form>
        </div>
    @endforeach
</div>

</body>
</html>