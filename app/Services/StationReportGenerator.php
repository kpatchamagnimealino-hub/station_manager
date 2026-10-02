<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Station;
use App\Models\StationStock;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Storage;

class StationReportGenerator
{
    public function csv(?CarbonInterface $date = null): string
    {
        $csv = fopen('php://temp', 'w+');
        fputcsv($csv, ['Type', 'Station', 'Produit', 'Nombre de ventes', 'Quantité vendue', 'Chiffre d’affaires', 'Stock', 'Seuil minimal', 'Statut'], ';');

        foreach ($this->stationPerformance($date) as $station) {
            fputcsv($csv, [
                'Ventes',
                $station->name,
                '',
                $station->sales_count,
                $station->sales_quantity,
                $station->sales_revenue,
                '',
                '',
                '',
            ], ';');
        }

        foreach (StationStock::with(['station', 'product'])->get() as $stock) {
            fputcsv($csv, [
                'Stock',
                $stock->station->name,
                $stock->product->name,
                '',
                '',
                '',
                $stock->quantity,
                $stock->product->minimum_stock,
                $stock->isLowStock() ? 'Alerte' : 'OK',
            ], ';');
        }

        rewind($csv);
        $content = "\xEF\xBB\xBF".stream_get_contents($csv);
        fclose($csv);

        return $content;
    }

    public function pdf(?CarbonInterface $date = null): string
    {
        $reportDate = $date ?? now();
        $lines = [
            'Rapport Station Manager - '.$reportDate->format('d/m/Y'),
            '',
            'VENTES PAR STATION',
        ];

        foreach ($this->stationPerformance($date) as $station) {
            $lines[] = $station->name.': '.$station->sales_count.' ventes, '.$station->sales_quantity.' litres, '.number_format((float) $station->sales_revenue, 0, ',', ' ').' FCFA';
        }

        $lines[] = '';
        $lines[] = 'STOCKS PAR STATION';

        foreach (StationStock::with(['station', 'product'])->get() as $stock) {
            $status = $stock->isLowStock() ? 'Alerte stock faible' : 'Stock normal';
            $lines[] = $stock->station->name.' - '.$stock->product->name.': '.$stock->quantity.' '.$stock->product->unit.' - '.$status;
        }

        return $this->buildPdf($lines);
    }

    /**
     * @return array{date: string, csv: string, pdf: string}
     */
    public function generateDaily(?CarbonInterface $date = null): array
    {
        $reportDate = $date ?? today();
        $dateLabel = $reportDate->format('Y-m-d');
        $directory = 'reports/daily/'.$dateLabel;
        $paths = [
            'date' => $dateLabel,
            'csv' => $directory.'/station-manager.csv',
            'pdf' => $directory.'/station-manager.pdf',
        ];

        Storage::disk('local')->put($paths['csv'], $this->csv($reportDate));
        Storage::disk('local')->put($paths['pdf'], $this->pdf($reportDate));

        return $paths;
    }

    /**
     * @return array<int, array{date: string, csv: bool, pdf: bool}>
     */
    public function recentDailyReports(int $days = 14): array
    {
        $disk = Storage::disk('local');
        $reports = [];

        for ($daysAgo = 0; $daysAgo < $days; $daysAgo++) {
            $date = today()->subDays($daysAgo)->format('Y-m-d');
            $directory = 'reports/daily/'.$date;
            $csvExists = $disk->exists($directory.'/station-manager.csv');
            $pdfExists = $disk->exists($directory.'/station-manager.pdf');

            if ($csvExists || $pdfExists) {
                $reports[] = [
                    'date' => $date,
                    'csv' => $csvExists,
                    'pdf' => $pdfExists,
                ];
            }
        }

        return $reports;
    }

    /**
     * @param  array<int, string>  $lines
     */
    private function buildPdf(array $lines): string
    {
        $pages = array_chunk($lines, 38);
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
        ];
        $pageReferences = [];

        foreach ($pages as $index => $pageLines) {
            $pageObject = 4 + ($index * 2);
            $contentObject = $pageObject + 1;
            $pageReferences[] = $pageObject.' 0 R';
            $stream = "BT\n/F1 10 Tf\n50 760 Td\n14 TL\n";

            foreach ($pageLines as $line) {
                $encodedLine = iconv('UTF-8', 'Windows-1252//TRANSLIT', $line) ?: $line;
                $encodedLine = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $encodedLine);
                $stream .= '('.$encodedLine.") Tj\nT*\n";
            }

            $stream .= "ET\n";
            $objects[$pageObject] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 3 0 R >> >> /Contents '.$contentObject.' 0 R >>';
            $objects[$contentObject] = '<< /Length '.strlen($stream).">>\nstream\n".$stream.'endstream';
        }

        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', $pageReferences).'] /Count '.count($pages).' >>';
        ksort($objects);
        $pdf = "%PDF-1.4\n%StationManager\n";
        $offsets = [0];

        foreach ($objects as $number => $object) {
            $offsets[$number] = strlen($pdf);
            $pdf .= $number." 0 obj\n".$object."\nendobj\n";
        }

        $crossReferenceOffset = strlen($pdf);
        $pdf .= "xref\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";

        for ($number = 1; $number <= count($objects); $number++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$number]);
        }

        return $pdf.'trailer'."\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n".$crossReferenceOffset."\n%%EOF";
    }

    private function stationPerformance(?CarbonInterface $date)
    {
        $sales = Sale::query()
            ->select('station_id')
            ->selectRaw('COUNT(*) as sales_count')
            ->selectRaw('COALESCE(SUM(quantity), 0) as sales_quantity')
            ->selectRaw('COALESCE(SUM(quantity * unit_price), 0) as sales_revenue')
            ->groupBy('station_id');

        if ($date !== null) {
            $sales->whereDate('sold_at', $date->toDateString());
        }

        return Station::query()
            ->leftJoinSub($sales, 'station_sales', 'stations.id', '=', 'station_sales.station_id')
            ->select('stations.*')
            ->selectRaw('COALESCE(station_sales.sales_count, 0) as sales_count')
            ->selectRaw('COALESCE(station_sales.sales_quantity, 0) as sales_quantity')
            ->selectRaw('COALESCE(station_sales.sales_revenue, 0) as sales_revenue')
            ->orderBy('stations.name')
            ->get();
    }
}
