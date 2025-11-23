<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Platform;

class GameController extends Controller
{
    public function index()
    {
        $games = Game::with('platform')->latest()->get();
        $platforms = Platform::all();
        return view('games', compact('games', 'platforms'));
    }

    public function dashboard()
    {
        $games = Game::with('platform')->latest()->get();
        $platforms = Platform::all();

        // Calculate statistics
        $totalGames = $games->count();
        $totalPlatforms = $platforms->count();
        $availableGames = $games->where('Availability', 'Available')->count();
        $comingSoon = $games->where('Availability', 'Coming Soon')->count();

        return view('dashboard', compact('games', 'platforms', 'totalGames', 'totalPlatforms', 'availableGames', 'comingSoon'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Game_name' => 'required|string|max:255',
            'Publisher' => 'required|string|max:255',
            'Availability' => 'required|string|max:255',
            'Description' => 'required|string',
            'platform_id' => 'required|exists:platforms,id',
        ]);
        Game::create($validated);
        return redirect()->back()->with('success', 'Game added successfully.');
    }

    public function update(Request $request, Game $game)
    {
        $validated = $request->validate([
            'Game_name' => 'required|string|max:255',
            'Publisher' => 'required|string|max:255',
            'Availability' => 'required|string|max:255',
            'Description' => 'required|string',
            'platform_id' => 'required|exists:platforms,id',
        ]);
        $game->update($validated);
        return redirect()->back()->with('success', 'Game updated successfully.');
    }

    public function destroy(Game $game)
    {
        $game->delete();
        return redirect()->back()->with('error', 'Game deleted successfully.');
    }

    public function gamesTable(Request $request)
    {
        $page = $request->get('page', 1);
        $games = Game::with('platform')->latest()->paginate(5, ['*'], 'page', $page);
        
        if ($request->ajax()) {
            return view('partials.games-table', compact('games'));
        }
        
        return view('partials.games-table', compact('games'));
    }
    
}