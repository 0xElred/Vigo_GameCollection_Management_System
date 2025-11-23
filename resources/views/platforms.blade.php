<!DOCTYPE html>
<html lang="en" x-data="{ openAddModal: false, editPlatform: null, deletePlatform: null }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platforms — Games UI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#0d1117] text-white font-sans min-h-screen flex">

    <!-- Sidebar -->
    <aside class="bg-[#161b22] w-64 h-screen fixed top-0 left-0 p-6 flex flex-col">
        <h3 class="text-[#58a6ff] text-2xl font-bold mb-8">Game Manager</h3>
        <nav class="flex-1">
            <a href="/dashboard" class="flex items-center gap-3 p-3 rounded-md hover:bg-[#21262d] hover:text-[#58a6ff]">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="/games" class="flex items-center gap-3 p-3 rounded-md hover:bg-[#21262d] hover:text-[#58a6ff]">
                <i class="fas fa-gamepad"></i> Games
            </a>
            <a href="/platforms" class="flex items-center gap-3 p-3 rounded-md bg-[#21262d] text-[#58a6ff]">
                <i class="fas fa-layer-group"></i> Platforms
            </a>
        </nav>
        <form method="POST" action="{{ route('logout') }}" class="mt-auto">
            @csrf
            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 py-2 rounded-md flex items-center justify-center gap-2">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-64 p-6">
        <div class="max-w-5xl mx-auto">

            <!-- Flash Messages -->
            @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="max-w-5xl mx-auto mb-4 px-4 py-3 rounded-lg bg-green-600 text-white shadow">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
            @endif

            @if(session('error'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="max-w-5xl mx-auto mb-4 px-4 py-3 rounded-lg bg-red-600 text-white shadow">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
            @endif

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold">Platforms</h1>
                    <p class="text-gray-400 mt-1">Manage your platforms</p>
                </div>
                <button @click="openAddModal = true" class="bg-[#238636] hover:bg-[#2ea043] px-4 py-2 rounded-lg font-semibold flex items-center gap-2 shadow-lg shadow-green-600/40">
                    <span class="text-lg">+</span> Add Platform
                </button>
            </div>
    
            <!-- Table Container -->
            <div class="bg-[#161b22] rounded-xl border border-[#1e232a] overflow-x-auto">
                <!-- Table Header -->
                <div class="grid grid-cols-2 px-6 py-3 text-gray-400 text-sm tracking-wide rounded-t-lg">
                    <div>Platform Name</div>
                    <div class="text-right">Actions</div>
                </div>
    
                <!-- Table Body -->
                <div class="bg-[#1e232a] rounded-b-lg">
                    @foreach($platforms as $platform)
                    <div class="grid grid-cols-2 items-center px-6 py-4 border-b border-[#0f131a] last:border-none">
                        <div class="text-white">{{ $platform->Platform_name }}</div>
                        <div class="flex justify-end gap-3">
                            <!-- Edit Button -->
                            <button 
                                @click="editPlatform = { id: {{ $platform->id }}, name: '{{ $platform->Platform_name }}' }" 
                                class="p-2 bg-[#0f131a] hover:bg-[#1a1f26] rounded-md">
                                <i class="fas fa-pen text-blue-500"></i>
                            </button>
    
                            <!-- Delete Button -->
                            <button 
                                @click="deletePlatform = { id: {{ $platform->id }}, name: '{{ $platform->Platform_name }}' }" 
                                class="p-2 bg-[#0f131a] hover:bg-[#1a1f26] rounded-md">
                                <i class="fas fa-trash text-red-500"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>

    <!-- Add Platform Modal -->
    <div x-show="openAddModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50" x-transition>
        <div class="bg-[#161b22] rounded-xl shadow-lg w-96 p-6 relative" @click.away="openAddModal = false">
            <h2 class="text-xl font-bold mb-4">Add Platform</h2>
            <form action="/platforms" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="Platform_name" class="block text-gray-300 mb-2">Platform Name</label>
                    <input type="text" id="Platform_name" name="Platform_name" required
                        class="w-full px-4 py-2 rounded-md bg-[#0f131a] border border-[#21262d] focus:outline-none focus:ring-2 focus:ring-[#238636]">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 rounded-md bg-gray-600 hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-md bg-[#238636] hover:bg-[#2ea043] text-white font-semibold">Add</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Platform Modal -->
    <div x-show="editPlatform" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50" x-transition>
        <div class="bg-[#161b22] rounded-xl shadow-lg w-96 p-6 relative" @click.away="editPlatform = null">
            <h2 class="text-xl font-bold mb-4">Edit Platform</h2>
            <form :action="`/platforms/${editPlatform?.id}`" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Platform Name</label>
                    <input type="text" name="Platform_name" x-model="editPlatform.name" required
                        class="w-full px-4 py-2 rounded-md bg-[#0f131a] border border-[#21262d] focus:outline-none focus:ring-2 focus:ring-[#238636]">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="editPlatform = null" class="px-4 py-2 rounded-md bg-gray-600 hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white font-semibold">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="deletePlatform" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50" x-transition>
        <div class="bg-[#161b22] rounded-xl shadow-lg w-96 p-6 relative" @click.away="deletePlatform = null">
            <div class="text-center mb-4">
                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-exclamation text-white text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-white">Delete Platform</h2>
                <p class="text-gray-300 mt-2">Are you sure you want to delete <span class="font-semibold text-white" x-text="deletePlatform?.name"></span>?</p>
                <p class="text-red-400 text-sm mt-2">This action cannot be undone!</p>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" @click="deletePlatform = null" class="px-4 py-2 rounded-md bg-gray-600 hover:bg-gray-700">Cancel</button>
                <form :action="`/platforms/${deletePlatform?.id}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white font-semibold">
                        Yes, Delete It
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>