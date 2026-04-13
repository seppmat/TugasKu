<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index() {
        $tasks = Task::latest()->get();
        return view('tasks', compact('tasks'));
    }

    public function store(Request $request) {
        Task::create([
            'title' => $request->title
        ]);
        return back();
    }

    public function update($id) {
        $task = Task::findOrFail($id);
        $task->is_done = !$task->is_done;
        $task->save();
        return back();
    }

    public function destroy($id) {
        Task::destroy($id);
        return back();
    }

}
