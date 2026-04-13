<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::today()->orderBy('status')->orderBy('created_at')->get();
        $completedCount = Task::today()->completed()->count();
        $totalCount = Task::today()->count();
        $progress = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 0;
        
        return view('tasks.index', compact('tasks', 'progress', 'completedCount', 'totalCount'));
    }
    
    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);
        Task::create(['title' => $request->title, 'status' => 'pending']);
        return redirect()->back();
    }
    
    public function update(Task $task, Request $request)
    {
        $task->update(['status' => $request->status]);
        return redirect()->back();
    }
    
    public function clearCompleted()
    {
        Task::completed()->delete();
        return redirect()->back();
    }

}
