<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Station;
use App\Models\StationStock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        return view('admin.sales', [
            'sales' => Sale::with(['station', 'product'])->latest('sold_at')->get(),
            'stations' => Station::all(),
            'products' => Product::all(),
            'stationStocks' => StationStock::with(['station', 'product'])->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.sales', [
            'sales' => Sale::with(['station', 'product'])->latest('sold_at')->get(),
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
            'sold_at' => ['required', 'date'],
        ]);

        $saleRecorded = DB::transaction(function () use ($validated): bool {
            $stationStock = StationStock::query()
                ->where('station_id', $validated['station_id'])
                ->where('product_id', $validated['product_id'])
                ->lockForUpdate()
                ->first();

            if (! $stationStock || $stationStock->quantity < $validated['quantity']) {
                return false;
            }

            Sale::create($validated);
            $stationStock->decrement('quantity', $validated['quantity']);

            return true;
        });

        if (! $saleRecorded) {
            return back()->withErrors(['quantity' => 'Le stock disponible est insuffisant.'])->withInput();
        }

        return redirect()->route('admin.sales.index')->with('success', 'Vente enregistrée.');
    }
}
