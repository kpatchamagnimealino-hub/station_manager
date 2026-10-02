<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Station;
use App\Models\StationStock;
use App\Models\User;
use App\Services\StationReportGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(Request $request): View
    {
        $stations = Station::count();
        $products = Product::all();
        $stationStocks = StationStock::with(['station', 'product'])->orderBy('station_id')->orderBy('product_id')->get();
        $lowStockStocks = $stationStocks->filter(fn (StationStock $stock) => $stock->isLowStock());
        $stationPerformance = $this->stationPerformance();
        $todaySales = $stationPerformance->sum('sales_revenue');
        $totalStock = $stationStocks->sum('quantity');
        $view = match ($request->user()->role) {
            User::ROLE_STATION_MANAGER => 'manager.dashboard',
            User::ROLE_GENERAL_DIRECTOR => 'director.dashboard',
            default => 'admin.dashboard',
        };

        return view($view, [
            'stations' => $stations,
            'products' => $products,
            'lowStockStocks' => $lowStockStocks,
            'todaySales' => $todaySales,
            'totalStock' => $totalStock,
            'stationStocks' => $stationStocks,
            'stationPerformance' => $stationPerformance,
        ]);
    }

    public function reports(StationReportGenerator $reportGenerator): View
    {
        $products = Product::orderBy('name')->get();
        $stations = $this->stationPerformance(false);
        $stationStocks = StationStock::with(['station', 'product'])->orderBy('station_id')->orderBy('product_id')->get();

        return view('admin.reports', [
            'products' => $products,
            'stations' => $stations,
            'stationStocks' => $stationStocks,
            'dailyReports' => $reportGenerator->recentDailyReports(),
        ]);
    }

    public function exportExcel(StationReportGenerator $reportGenerator)
    {
        return response($reportGenerator->csv())
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="rapport_stations.csv"');
    }

    public function exportPdf(StationReportGenerator $reportGenerator)
    {
        return response($reportGenerator->pdf())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="rapport_station_manager.pdf"');
    }

    public function downloadDailyReport(string $date, string $format)
    {
        $path = "reports/daily/{$date}/station-manager.{$format}";

        abort_unless(preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1, 404);
        abort_unless(in_array($format, ['csv', 'pdf'], true), 404);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }

    private function stationPerformance(bool $todayOnly = true)
    {
        $dailySales = Sale::query()
            ->select('station_id')
            ->selectRaw('COUNT(*) as sales_count')
            ->selectRaw('COALESCE(SUM(quantity), 0) as sales_quantity')
            ->selectRaw('COALESCE(SUM(quantity * unit_price), 0) as sales_revenue')
            ->groupBy('station_id');

        if ($todayOnly) {
            $dailySales->whereDate('sold_at', today());
        }

        return Station::query()
            ->leftJoinSub($dailySales, 'station_sales', 'stations.id', '=', 'station_sales.station_id')
            ->select('stations.*')
            ->selectRaw('COALESCE(station_sales.sales_count, 0) as sales_count')
            ->selectRaw('COALESCE(station_sales.sales_quantity, 0) as sales_quantity')
            ->selectRaw('COALESCE(station_sales.sales_revenue, 0) as sales_revenue')
            ->orderBy('stations.name')
            ->get();
    }
}
