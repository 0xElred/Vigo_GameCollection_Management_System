@foreach($games as $game)
<div class="grid grid-cols-6 items-center px-6 py-4 border-b border-[#0f131a] last:border-none" 
     data-game-id="{{ $game->id }}"
     data-game-name="{{ $game->Game_name }}"
     data-game-publisher="{{ $game->Publisher }}"
     data-platform-id="{{ $game->platform_id }}"
     data-availability="{{ $game->Availability }}"
     data-description="{{ $game->Description }}">
    
    <div class="text-white font-medium">{{ $game->Game_name }}</div>
    <div class="text-gray-300">{{ $game->Publisher }}</div>
    <div class="text-gray-300">{{ $game->platform->Platform_name }}</div>
    <div>
        <button class="text-[#58a6ff] hover:text-[#79c0ff] text-sm font-medium underline description-btn">
            Read Description
        </button>
    </div>
    <div>
        @if($game->Availability === 'Available')
        <span class="px-2 py-1 bg-green-600 text-white text-xs rounded-full">Available</span>
        @elseif($game->Availability === 'Coming Soon')
        <span class="px-2 py-1 bg-yellow-600 text-white text-xs rounded-full">Coming Soon</span>
        @else
        <span class="px-2 py-1 bg-red-600 text-white text-xs rounded-full">Unavailable</span>
        @endif
    </div>
    <div class="flex justify-end gap-3">
        <!-- Edit Button -->
        <button class="p-2 bg-[#0f131a] hover:bg-[#1a1f26] rounded-md">
            <i class="fas fa-pen text-blue-500"></i>
        </button>

        <!-- Delete Button -->
        <button class="p-2 bg-[#0f131a] hover:bg-[#1a1f26] rounded-md">
            <i class="fas fa-trash text-red-500"></i>
        </button>
    </div>
</div>
@endforeach