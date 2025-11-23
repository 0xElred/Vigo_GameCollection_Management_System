<!DOCTYPE html>
<html lang="en" x-data="{ openAddModal: false, editGame: null, deleteGame: null, showDescription: null }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Games — Games UI</title>
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
            <a href="/games" class="flex items-center gap-3 p-3 rounded-md bg-[#21262d] text-[#58a6ff]">
                <i class="fas fa-gamepad"></i> Games
            </a>
            <a href="/platforms" class="flex items-center gap-3 p-3 rounded-md hover:bg-[#21262d] hover:text-[#58a6ff]">
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
        <div class="max-w-7xl mx-auto">

            <!-- Flash Messages -->
            @if(session('success'))
            <div x-data="{ show: true }" 
                 x-show="show" 
                 x-init="setTimeout(() => show = false, 3000)"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="max-w-7xl mx-auto mb-4 px-4 py-3 rounded-lg bg-green-600 text-white shadow">
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
                 class="max-w-7xl mx-auto mb-4 px-4 py-3 rounded-lg bg-red-600 text-white shadow">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    {{ session('error') }}
                </div>
            </div>
            @endif

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold">Games</h1>
                    <p class="text-gray-400 mt-1">Manage your game collection</p>
                </div>
                <button @click="openAddModal = true" class="bg-[#238636] hover:bg-[#2ea043] px-4 py-2 rounded-lg font-semibold flex items-center gap-2 shadow-lg shadow-green-600/40">
                    <span class="text-lg">+</span> Add Game
                </button>
            </div>
    
            <!-- Table Container -->
            <div class="bg-[#161b22] rounded-xl border border-[#1e232a] overflow-x-auto">
                <!-- Table Header -->
                <div class="grid grid-cols-6 px-6 py-3 text-gray-400 text-sm tracking-wide rounded-t-lg">
                    <div>Game Name</div>
                    <div>Publisher</div>
                    <div>Platform</div>
                    <div>Availability</div>
                    <div>Description</div>
                    <div class="text-right">Actions</div>
                </div>
    
                <!-- Table Body -->
                <div class="bg-[#1e232a] rounded-b-lg">
                    @foreach($games as $game)
                    <div class="grid grid-cols-6 items-center px-6 py-4 border-b border-[#0f131a] last:border-none">
                        <div class="text-white font-medium">{{ $game->Game_name }}</div>
                        <div class="text-gray-300">{{ $game->Publisher }}</div>
                        <div class="text-gray-300">{{ $game->platform->Platform_name }}</div>
                        <div>
                            @if($game->Availability === 'Available')
                            <span class="px-2 py-1 bg-green-600 text-white text-xs rounded-full">Available</span>
                            @elseif($game->Availability === 'Coming Soon')
                            <span class="px-2 py-1 bg-yellow-600 text-white text-xs rounded-full">Coming Soon</span>
                            @else
                            <span class="px-2 py-1 bg-red-600 text-white text-xs rounded-full">Unavailable</span>
                            @endif
                        </div>
                        <div>
                            <button 
                                @click="showDescription = { 
                                    name: '{{ $game->Game_name }}', 
                                    description: `{{ $game->Description }}`
                                }" 
                                class="text-[#58a6ff] hover:text-[#79c0ff] text-sm font-medium underline">
                                Read Description
                            </button>
                        </div>
                        <div class="flex justify-end gap-3">
                            <!-- Edit Button -->
                            <button 
                                @click="editGame = { 
                                    id: {{ $game->id }}, 
                                    name: '{{ $game->Game_name }}', 
                                    publisher: '{{ $game->Publisher }}', 
                                    platform_id: {{ $game->platform_id }},
                                    availability: '{{ $game->Availability }}',
                                    description: `{{ $game->Description }}`
                                }" 
                                class="p-2 bg-[#0f131a] hover:bg-[#1a1f26] rounded-md">
                                <i class="fas fa-pen text-blue-500"></i>
                            </button>
    
                            <!-- Delete Button -->
                            <button 
                                @click="deleteGame = { id: {{ $game->id }}, name: '{{ $game->Game_name }}' }" 
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

    <!-- Add Game Modal -->
    <div x-show="openAddModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50" x-transition>
        <div class="bg-[#161b22] rounded-xl shadow-lg w-96 p-6 relative" @click.away="openAddModal = false">
            <h2 class="text-xl font-bold mb-4">Add Game</h2>
            <form action="/games" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="Game_name" class="block text-gray-300 mb-2">Game Name</label>
                    <input type="text" id="Game_name" name="Game_name" required
                        class="w-full px-4 py-2 rounded-md bg-[#0f131a] border border-[#21262d] focus:outline-none focus:ring-2 focus:ring-[#238636]">
                </div>
                <div class="mb-4">
                    <label for="Publisher" class="block text-gray-300 mb-2">Publisher</label>
                    <input type="text" id="Publisher" name="Publisher" required
                        class="w-full px-4 py-2 rounded-md bg-[#0f131a] border border-[#21262d] focus:outline-none focus:ring-2 focus:ring-[#238636]">
                </div>
                <div class="mb-4">
                    <label for="platform_id" class="block text-gray-300 mb-2">Platform</label>
                    <select id="platform_id" name="platform_id" required
                        class="w-full px-4 py-2 rounded-md bg-[#0f131a] border border-[#21262d] focus:outline-none focus:ring-2 focus:ring-[#238636]">
                        <option value="">Select Platform</option>
                        @foreach($platforms as $platform)
                        <option value="{{ $platform->id }}">{{ $platform->Platform_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="Availability" class="block text-gray-300 mb-2">Availability</label>
                    <select id="Availability" name="Availability" required
                        class="w-full px-4 py-2 rounded-md bg-[#0f131a] border border-[#21262d] focus:outline-none focus:ring-2 focus:ring-[#238636]">
                        <option value="Available">Available</option>
                        <option value="Coming Soon">Coming Soon</option>
                        <option value="Unavailable">Unavailable</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="Description" class="block text-gray-300 mb-2">Description</label>
                    <textarea id="Description" name="Description" rows="3" required
                        class="w-full px-4 py-2 rounded-md bg-[#0f131a] border border-[#21262d] focus:outline-none focus:ring-2 focus:ring-[#238636]"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="openAddModal = false" class="px-4 py-2 rounded-md bg-gray-600 hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-md bg-[#238636] hover:bg-[#2ea043] text-white font-semibold">Add Game</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Game Modal -->
    <div x-show="editGame" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50" x-transition>
        <div class="bg-[#161b22] rounded-xl shadow-lg w-96 p-6 relative" @click.away="editGame = null">
            <h2 class="text-xl font-bold mb-4">Edit Game</h2>
            <form :action="`/games/${editGame?.id}`" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Game Name</label>
                    <input type="text" name="Game_name" x-model="editGame.name" required
                        class="w-full px-4 py-2 rounded-md bg-[#0f131a] border border-[#21262d] focus:outline-none focus:ring-2 focus:ring-[#238636]">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Publisher</label>
                    <input type="text" name="Publisher" x-model="editGame.publisher" required
                        class="w-full px-4 py-2 rounded-md bg-[#0f131a] border border-[#21262d] focus:outline-none focus:ring-2 focus:ring-[#238636]">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Platform</label>
                    <select name="platform_id" x-model="editGame.platform_id" required
                        class="w-full px-4 py-2 rounded-md bg-[#0f131a] border border-[#21262d] focus:outline-none focus:ring-2 focus:ring-[#238636]">
                        @foreach($platforms as $platform)
                        <option value="{{ $platform->id }}">{{ $platform->Platform_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Availability</label>
                    <select name="Availability" x-model="editGame.availability" required
                        class="w-full px-4 py-2 rounded-md bg-[#0f131a] border border-[#21262d] focus:outline-none focus:ring-2 focus:ring-[#238636]">
                        <option value="Available">Available</option>
                        <option value="Coming Soon">Coming Soon</option>
                        <option value="Unavailable">Unavailable</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2">Description</label>
                    <textarea name="Description" x-model="editGame.description" rows="3" required
                        class="w-full px-4 py-2 rounded-md bg-[#0f131a] border border-[#21262d] focus:outline-none focus:ring-2 focus:ring-[#238636]"></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="editGame = null" class="px-4 py-2 rounded-md bg-gray-600 hover:bg-gray-700">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white font-semibold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="deleteGame" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50" x-transition>
        <div class="bg-[#161b22] rounded-xl shadow-lg w-96 p-6 relative" @click.away="deleteGame = null">
            <div class="text-center mb-4">
                <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-exclamation text-white text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-white">Delete Game</h2>
                <p class="text-gray-300 mt-2">Are you sure you want to delete <span class="font-semibold text-white" x-text="deleteGame?.name"></span>?</p>
                <p class="text-red-400 text-sm mt-2">This action cannot be undone!</p>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" @click="deleteGame = null" class="px-4 py-2 rounded-md bg-gray-600 hover:bg-gray-700">Cancel</button>
                <form :action="`/games/${deleteGame?.id}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white font-semibold">
                        Yes, Delete It
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Description Popup Modal -->
    <div x-show="showDescription" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50" x-transition>
        <div class="bg-[#161b22] rounded-xl shadow-lg w-2/5 p-6 relative" @click.away="showDescription = null">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-white" x-text="showDescription?.name + ' - Description'"></h2>
                <button @click="showDescription = null" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="bg-[#0f131a] rounded-lg p-4 border border-[#21262d]">
                <p class="text-gray-300 whitespace-pre-wrap" x-text="showDescription?.description"></p>
            </div>
            <div class="flex justify-end mt-4">
                <button @click="showDescription = null" class="px-4 py-2 rounded-md bg-gray-600 hover:bg-gray-700">
                    Close
                </button>
            </div>
        </div>
    </div>

</body>
</html>