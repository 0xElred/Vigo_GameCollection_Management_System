<!DOCTYPE html>
<html lang="en" x-data="{ openAddModal: false, editGame: null, deleteGame: null, showDescription: null, currentPage: 1, totalPages: {{ ceil($games->count() / 5) }}, loading: false, sidebarOpen: false }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Games UI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Smooth sidebar transitions */
        .sidebar-transition {
            transition: transform 0.3s ease-in-out;
        }
        
        /* Mobile overlay for sidebar */
        @media (max-width: 768px) {
            .sidebar-overlay {
                display: none;
            }
            .sidebar-open .sidebar-overlay {
                display: block;
            }
        }
        
        /* Ensure table horizontal scrolling */
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>
<body class="bg-[#0d1117] text-white font-sans min-h-screen flex" 
      x-data="dashboard()" 
      :class="{ 'sidebar-open': sidebarOpen, 'overflow-hidden md:overflow-auto': sidebarOpen }">

    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden" 
         x-show="sidebarOpen" 
         @click="sidebarOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
    </div>

    <!-- Sidebar -->
    <aside class="bg-[#161b22] w-64 h-screen fixed top-0 left-0 p-6 flex flex-col z-50 sidebar-transition"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
           <div class="flex items-center justify-center mb-8">
            <img src="{{ asset('storage/logo.png') }}" alt="Game Manager Logo" class="w-16 h-16 rounded-xl">
            <!-- Close button for mobile -->
            <button @click="sidebarOpen = false" class="md:hidden text-gray-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="flex-1">
            <a href="/dashboard" class="flex items-center gap-3 p-3 rounded-md bg-[#21262d] text-[#58a6ff]">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="/games" class="flex items-center gap-3 p-3 rounded-md hover:bg-[#21262d] hover:text-[#58a6ff]">
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
    <main class="flex-1 md:ml-64 p-4 md:p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Mobile Header with Menu Button -->
            <div class="flex items-center gap-4 mb-6 md:hidden">
                <button @click="sidebarOpen = true" class="p-2 rounded-md bg-[#161b22] hover:bg-[#21262d]">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                <div>
                    <h1 class="text-2xl font-bold">Dashboard</h1>
                    <p class="text-gray-400 text-sm">Overview of your game collection</p>
                </div>
            </div>

            <!-- Desktop Header -->
            <div class="hidden md:flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold">Dashboard</h1>
                    <p class="text-gray-400 mt-1">Overview of your game collection</p>
                </div>
                <button @click="openAddModal = true" class="bg-[#238636] hover:bg-[#2ea043] px-4 py-2 rounded-lg font-semibold flex items-center gap-2 shadow-lg shadow-green-600/40">
                    <span class="text-lg">+</span> Add Game
                </button>
            </div>

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

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
                <!-- Total Games Card -->
                <div class="bg-[#161b22] rounded-xl border border-[#1e232a] p-4 md:p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm">Total Games</p>
                            <h3 class="text-xl md:text-2xl font-bold text-white mt-1">{{ $totalGames }}</h3>
                        </div>
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-gamepad text-white text-lg md:text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-[#1e232a]">
                        <p class="text-green-400 text-sm">
                            <i class="fas fa-chart-line mr-1"></i>
                            Your complete collection
                        </p>
                    </div>
                </div>

                <!-- Total Platforms Card -->
                <div class="bg-[#161b22] rounded-xl border border-[#1e232a] p-4 md:p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm">Platforms</p>
                            <h3 class="text-xl md:text-2xl font-bold text-white mt-1">{{ $totalPlatforms }}</h3>
                        </div>
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-layer-group text-white text-lg md:text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-[#1e232a]">
                        <p class="text-blue-400 text-sm">
                            <i class="fas fa-cube mr-1"></i>
                            Gaming platforms
                        </p>
                    </div>
                </div>

                <!-- Available Games Card -->
                <div class="bg-[#161b22] rounded-xl border border-[#1e232a] p-4 md:p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm">Available</p>
                            <h3 class="text-xl md:text-2xl font-bold text-white mt-1">{{ $availableGames }}</h3>
                        </div>
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-green-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-check-circle text-white text-lg md:text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-[#1e232a]">
                        <p class="text-green-400 text-sm">
                            <i class="fas fa-check mr-1"></i>
                            Ready to play
                        </p>
                    </div>
                </div>

                <!-- Coming Soon Card -->
                <div class="bg-[#161b22] rounded-xl border border-[#1e232a] p-4 md:p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-400 text-sm">Coming Soon</p>
                            <h3 class="text-xl md:text-2xl font-bold text-white mt-1">{{ $comingSoon }}</h3>
                        </div>
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-yellow-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-white text-lg md:text-xl"></i>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-[#1e232a]">
                        <p class="text-yellow-400 text-sm">
                            <i class="fas fa-hourglass-half mr-1"></i>
                            Upcoming releases
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recent Games Table -->
            <div class="bg-[#161b22] rounded-xl border border-[#1e232a]">
                <!-- Table Container for Horizontal Scrolling -->
                <div class="table-container">
                    <!-- Table Header -->
                    <div class="grid grid-cols-6 px-4 md:px-6 py-3 text-gray-400 text-sm tracking-wide rounded-t-lg min-w-[800px]">
                        <div class="px-2">Game Name</div>
                        <div class="px-2">Publisher</div>
                        <div class="px-2">Platform</div>
                        <div class="px-2">Description</div>
                        <div class="px-2">Availability</div>
                        <div class="px-2 text-right">Actions</div>
                    </div>
        
                    <!-- Table Body -->
                    <div id="gamesTableBody" class="bg-[#1e232a] rounded-b-lg min-w-[800px]" @click="handleTableClick($event)">
                        @include('partials.games-table', ['games' => $games->take(5)])
                    </div>
                </div>

                <!-- Loading Spinner -->
                <div x-show="loading" class="flex justify-center items-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#58a6ff]"></div>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col sm:flex-row justify-between items-center px-4 md:px-6 py-4 border-t border-[#0f131a] gap-4">
                    <div class="text-gray-400 text-sm text-center sm:text-left">
                        Showing <span x-text="(currentPage - 1) * 5 + 1"></span> to <span x-text="Math.min(currentPage * 5, {{ $games->count() }})"></span> of {{ $games->count() }} results
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <!-- Previous Button -->
                        <button 
                            @click="previousPage()"
                            :disabled="currentPage === 1"
                            :class="currentPage === 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#1a1f26]'"
                            class="p-2 bg-[#0f131a] rounded-md">
                            <i class="fas fa-chevron-left text-gray-400"></i>
                        </button>

                        <!-- Page Numbers -->
                        <template x-for="page in getPageNumbers()" :key="page">
                            <button 
                                @click="goToPage(page)"
                                :class="page === currentPage ? 'bg-[#58a6ff] text-white' : 'bg-[#0f131a] text-gray-400 hover:bg-[#1a1f26]'"
                                class="w-8 h-8 rounded-md text-sm font-medium"
                                x-text="page">
                            </button>
                        </template>

                        <!-- Next Button -->
                        <button 
                            @click="nextPage()"
                            :disabled="currentPage === totalPages"
                            :class="currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#1a1f26]'"
                            class="p-2 bg-[#0f131a] rounded-md">
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                <div class="bg-[#161b22] rounded-xl border border-[#1e232a] p-4 md:p-6">
                    <h3 class="text-lg font-semibold mb-4 text-[#58a6ff]">Quick Actions</h3>
                    <div class="space-y-3">
                        <button @click="openAddModal = true" class="w-full flex items-center gap-3 p-3 rounded-md bg-[#0f131a] hover:bg-[#1a1f26] text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-plus text-green-500"></i>
                            <span>Add New Game</span>
                        </button>
                        <a href="/platforms" class="flex items-center gap-3 p-3 rounded-md bg-[#0f131a] hover:bg-[#1a1f26] text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-layer-group text-blue-500"></i>
                            <span>Manage Platforms</span>
                        </a>
                        <a href="/games" class="flex items-center gap-3 p-3 rounded-md bg-[#0f131a] hover:bg-[#1a1f26] text-gray-300 hover:text-white transition-colors">
                            <i class="fas fa-list text-yellow-500"></i>
                            <span>View All Games</span>
                        </a>
                    </div>
                </div>

                <div class="bg-[#161b22] rounded-xl border border-[#1e232a] p-4 md:p-6">
                    <h3 class="text-lg font-semibold mb-4 text-[#58a6ff]">Collection Stats</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Available Rate</span>
                            <span class="text-green-400 font-semibold">
                                @if($totalGames > 0)
                                    {{ number_format(($availableGames / $totalGames) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Upcoming Rate</span>
                            <span class="text-yellow-400 font-semibold">
                                @if($totalGames > 0)
                                    {{ number_format(($comingSoon / $totalGames) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Games per Platform</span>
                            <span class="text-blue-400 font-semibold">
                                @if($totalPlatforms > 0)
                                    {{ number_format($totalGames / $totalPlatforms, 1) }}
                                @else
                                    0
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Game Modal -->
    <div x-show="openAddModal" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4" x-transition>
        <div class="bg-[#161b22] rounded-xl shadow-lg w-full max-w-md p-6 relative" @click.away="openAddModal = false">
            <h2 class="text-xl font-bold mb-4">Add Game</h2>
            <form action="/games" method="POST" @submit="openAddModal = false">
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
    <div x-show="editGame" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4" x-transition>
        <div class="bg-[#161b22] rounded-xl shadow-lg w-full max-w-md p-6 relative" @click.away="editGame = null">
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
    <div x-show="deleteGame" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4" x-transition>
        <div class="bg-[#161b22] rounded-xl shadow-lg w-full max-w-md p-6 relative" @click.away="deleteGame = null">
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
    <div x-show="showDescription" class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4" x-transition>
        <div class="bg-[#161b22] rounded-xl shadow-lg w-full max-w-2xl p-6 relative" @click.away="showDescription = null">
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

    <script>
        function dashboard() {
            return {
                openAddModal: false,
                editGame: null,
                deleteGame: null,
                showDescription: null,
                currentPage: 1,
                totalPages: {{ ceil($games->count() / 5) }},
                loading: false,
                sidebarOpen: false,

                handleTableClick(event) {
                    const button = event.target.closest('button');
                    if (!button) return;

                    const gameRow = button.closest('[data-game-id]');
                    if (!gameRow) return;

                    const gameId = gameRow.dataset.gameId;
                    const gameName = gameRow.dataset.gameName;
                    const gamePublisher = gameRow.dataset.gamePublisher;
                    const platformId = gameRow.dataset.platformId;
                    const availability = gameRow.dataset.availability;
                    const description = gameRow.dataset.description;

                    // Edit button
                    if (button.querySelector('.fa-pen')) {
                        this.editGame = {
                            id: gameId,
                            name: gameName,
                            publisher: gamePublisher,
                            platform_id: platformId,
                            availability: availability,
                            description: description
                        };
                    }
                    // Delete button
                    else if (button.querySelector('.fa-trash')) {
                        this.deleteGame = {
                            id: gameId,
                            name: gameName
                        };
                    }
                    // Read Description button
                    else if (button.classList.contains('description-btn')) {
                        this.showDescription = {
                            name: gameName,
                            description: description
                        };
                    }
                },

                previousPage() {
                    if (this.currentPage > 1) {
                        this.goToPage(this.currentPage - 1);
                    }
                },

                nextPage() {
                    if (this.currentPage < this.totalPages) {
                        this.goToPage(this.currentPage + 1);
                    }
                },

                goToPage(page) {
                    if (page < 1 || page > this.totalPages) return;
                    
                    this.loading = true;
                    this.currentPage = page;

                    fetch(`/dashboard/games-table?page=${page}`)
                        .then(response => response.text())
                        .then(html => {
                            document.getElementById('gamesTableBody').innerHTML = html;
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Error loading games:', error);
                            this.loading = false;
                        });
                },

                getPageNumbers() {
                    const current = this.currentPage;
                    const total = this.totalPages;
                    const delta = 2;
                    const range = [];
                    const rangeWithDots = [];

                    for (let i = 1; i <= total; i++) {
                        if (i === 1 || i === total || (i >= current - delta && i <= current + delta)) {
                            range.push(i);
                        }
                    }

                    let prev = 0;
                    for (let i of range) {
                        if (prev) {
                            if (i - prev === 2) {
                                rangeWithDots.push(prev + 1);
                            } else if (i - prev !== 1) {
                                rangeWithDots.push('...');
                            }
                        }
                        rangeWithDots.push(i);
                        prev = i;
                    }

                    return rangeWithDots;
                }
            }
        }
    </script>

</body>
</html>