<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Station;
use App\Models\StationStock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StationStockController extends Controller
{
    public function index(): View
    {
        return view('admin.stocks', [
            'stationStocks' => StationStock::with(['station', 'product'])->orderBy('station_id')->orderBy('product_id')->get(),
            'stations' => Station::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
            'legacyStockProducts' => Product::where('stock_quantity', '>', 0)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'station_id' => ['required', 'exists:stations,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'numeric', 'min:0'],
        ]);

        StationStock::updateOrCreate(
            [
                'station_id' => $validated['station_id'],
                'product_id' => $validated['product_id'],
            ],
            ['quantity' => $validated['quantity']],
        );

        return redirect()->route('admin.stocks.index')->with('success', 'Stock de la station mis à jour.');
    }
}
