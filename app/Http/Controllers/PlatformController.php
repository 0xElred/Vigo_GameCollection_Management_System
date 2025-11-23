<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Platform;

class PlatformController extends Controller
{
    public function index()
    {
        $platforms = Platform::all();
        return view('platforms', compact('platforms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Platform_name' => 'required|string|max:255',
        ]);
        Platform::create($validated);
        return redirect()->back()->with('success', 'Platform added successfully.');
    }

    public function update(Request $request, Platform $platform)
    {
        $validated = $request->validate([
            'Platform_name' => 'required|string|max:255',
        ]);
        $platform->update($validated);
        return redirect()->back()->with('success', 'Platform updated successfully.');
    }
    public function destroy(Platform $platform)
    {
        $platform->delete();
        return redirect()->back()->with('error', 'Platform deleted successfully.');
    }

}
