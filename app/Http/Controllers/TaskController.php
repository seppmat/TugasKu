<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {        
        $tasks = Task::orderBy('status')
                     ->orderBy('created_at', 'desc')
                     ->get();
        
        // Hitung progress
        $total = $tasks->count();
        $completed = $tasks->where('status', 'completed')->count();
        $progress = $total > 0 ? round(($completed / $total) * 100) : 0;
        
        return view('tasks.index', compact('tasks', 'progress', 'completed', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        Task::create([
            'title' => $request->title,
            'status' => 'pending'
        ]);

        return redirect()->back();
    }

    public function update(Task $task, Request $request)
    {
        // Toggle status: completed ↔ pending
        $newStatus = $task->status === 'completed' ? 'pending' : 'completed';
        $task->update(['status' => $newStatus]);
        
        return redirect()->back();
    }

    public function clearCompleted()
    {
        Task::where('status', 'completed')->delete();
        return redirect()->back();
    }
}