<!-- resources/views/tasks/index.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoodleDo - Tasks</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#B91C5C',
                        secondary: '#FDE8FC',
                        accent: '#6EE7B7',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-stone-50 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-stone-100 p-6">
            <h1 class="text-2xl font-bold text-primary mb-1">DoodleDo</h1>
            <p class="text-sm text-gray-500 mb-8">My Scrapbook</p>
            
            <nav class="space-y-3">
                <a href="#" class="flex items-center gap-3 text-primary font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    All Tasks
                </a>
                <a href="#" class="flex items-center gap-3 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    In Progress
                </a>
                <a href="#" class="flex items-center gap-3 text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Completed
                </a>
            </nav>
            
            <div class="absolute bottom-6 left-6 right-6">
                <button class="w-full bg-accent/50 text-teal-800 py-2 rounded-full font-medium">Quick Note</button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8 overflow-y-auto">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Today's Bits ⭐</h2>
                <p class="text-gray-600 mb-6">Gather your thoughts, one doodle at a time.</p>

                <!-- Input -->
                <form action="{{ route('tasks.store') }}" method="POST" class="mb-8">
                    @csrf
                    <div class="flex gap-3">
                        <input type="text" name="title" placeholder="What's on your mind today?" 
                               class="flex-1 px-6 py-4 rounded-full border-0 shadow-sm focus:ring-2 focus:ring-primary/20">
                        <button type="submit" class="w-12 h-12 bg-primary text-white rounded-full hover:bg-primary/90">
                            <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </button>
                    </div>
                </form>

                <!-- Tasks List -->
                <div class="space-y-3">
                    @foreach($tasks as $task)
                    <form action="{{ route('tasks.update', $task) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="flex items-center gap-4 p-4 bg-white/60 rounded-2xl hover:bg-white transition">
                            <input type="hidden" name="status" value="{{ $task->status === 'completed' ? 'pending' : 'completed' }}">
                            <button type="submit" class="w-6 h-6 rounded-full border-2 {{ $task->status === 'completed' ? 'bg-teal-600 border-teal-600' : 'border-primary' }} flex items-center justify-center">
                                @if($task->status === 'completed')
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                @endif
                            </button>
                            <span class="flex-1 {{ $task->status === 'completed' ? 'line-through text-gray-400' : 'text-gray-800' }}">
                                {{ $task->title }}
                            </span>
                            @if($task->status === 'in_progress')
                            <span class="px-3 py-1 bg-yellow-200 text-yellow-800 text-xs rounded-full">In Progress</span>
                            @endif
                        </div>
                    </form>
                    @endforeach
                </div>

                <!-- Progress & Quote -->
                <div class="grid grid-cols-2 gap-6 mt-8">
                    <div class="bg-pink-100 p-6 rounded-3xl">
                        <h3 class="text-xl font-bold text-primary mb-3">Daily Bloom</h3>
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex-1 h-3 bg-white/60 rounded-full overflow-hidden">
                                <div class="h-full bg-primary rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                            <span class="font-bold text-primary">{{ number_format($progress, 0) }}%</span>
                        </div>
                        <p class="text-sm text-gray-700">You're doing wonderful! Just {{ 2 - $completedCount }} more tasks to complete your garden for today.</p>
                    </div>
                    
                    <div class="bg-yellow-50 p-6 rounded-3xl border-2 border-dashed border-yellow-300">
                        <h3 class="text-xl font-bold text-amber-800 mb-2">Quick Spark</h3>
                        <p class="text-amber-700 italic">"Creativity is intelligence having fun."</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>