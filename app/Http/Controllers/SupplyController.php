<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Station;
use App\Models\StationStock;
use App\Models\Supply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupplyController extends Controller
{
    public function index(): View
    {
        return view('admin.supplies', [
            'supplies' => Supply::with(['station', 'product'])->latest('received_at')->get(),
            'stations' => Station::all(),
            'products' => Product::all(),
            'stationStocks' => StationStock::with(['station', 'product'])->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.supplies', [
            'supplies' => Supply::with(['station', 'product'])->latest('received_at')->get(),
            'stations' => Station::all(),
            'products' => Product::all(),
            'stationStocks' => StationStock::with(['station', 'product'])->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'station_id' => ['required', 'exists:stations,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'received_at' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($validated): void {
            $stationStock = StationStock::firstOrCreate(
                [
                    'station_id' => $validated['station_id'],
                    'product_id' => $validated['product_id'],
                ],
                ['quantity' => 0],
            );
            $stationStock = StationStock::query()->lockForUpdate()->findOrFail($stationStock->id);

            Supply::create($validated);
            $stationStock->increment('quantity', $validated['quantity']);
        });

        return redirect()->route('admin.supplies.index')->with('success', 'Approvisionnement enregistré.');
    }
}
