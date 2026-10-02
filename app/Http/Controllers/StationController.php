<?php

namespace App\Http\Controllers;

use App\Models\Station;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StationController extends Controller
{
    public function index(): View
    {
        return view('admin.stations', [
            'stations' => Station::latest()->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.stations', [
            'stations' => Station::latest()->get(),
            'editing' => false,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        Station::create($validated);

        return redirect()->route('admin.stations.index')->with('success', 'Station ajoutée avec succès.');
    }

    public function update(Request $request, Station $station): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $station->update($validated);

        return redirect()->route('admin.stations.index')->with('success', 'Station mise à jour.');
    }
}
