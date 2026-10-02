<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Rapports et statistiques</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <div class="mx-auto max-w-6xl px-6 py-10">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">Station Manager</p>
                    <h1 class="text-3xl font-bold">Rapports & statistiques</h1>
                </div>
                <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Retour</a>
            </div>

            <div class="mb-6 flex flex-wrap items-center gap-4">
                <a href="{{ route('admin.reports.pdf') }}" class="rounded-lg bg-slate-900 px-4 py-3 font-medium text-white hover:bg-slate-800">Exporter PDF</a>
                <a href="{{ route('admin.reports.excel') }}" class="rounded-lg bg-emerald-600 px-4 py-3 font-medium text-white hover:bg-emerald-700">Exporter Excel</a>
                <span class="text-sm text-slate-500">Export Excel compatible (CSV)</span>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                    <h2 class="mb-4 text-xl font-bold">Stocks par station</h2>
                    <div class="space-y-3">
                        @forelse ($stationStocks as $stock)
                            <div class="flex items-center justify-between rounded-xl bg-slate-50 p-4">
                                <div>
                                    <p class="font-semibold">{{ $stock->station->name }} · {{ $stock->product->name }}</p>
                                    <p class="text-sm text-slate-500">Seuil : {{ $stock->product->minimum_stock }} {{ $stock->product->unit }}</p>
                                </div>
                                <span class="font-semibold {{ $stock->isLowStock() ? 'text-red-700' : 'text-emerald-700' }}">{{ $stock->quantity }} {{ $stock->product->unit }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Aucun stock d’ouverture saisi par station.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                    <h2 class="mb-4 text-xl font-bold">Performance stations</h2>
                    <div class="space-y-3">
                        @foreach ($stations as $station)
                            <div class="rounded-xl bg-slate-50 p-4">
                                <div class="flex items-center justify-between">
                                    <span class="font-semibold">{{ $station->name }}</span>
                                    <span class="font-semibold text-slate-700">{{ $station->sales_count }} ventes</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500">{{ number_format((float) $station->sales_quantity, 0, ',', ' ') }} L vendus · {{ number_format((float) $station->sales_revenue, 0, ',', ' ') }} FCFA</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <section class="mt-6 rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                <div class="mb-4">
                    <h2 class="text-xl font-bold">Archives quotidiennes</h2>
                    <p class="mt-1 text-sm text-slate-500">Les rapports sont générés chaque jour à 23 h 55.</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($dailyReports as $report)
                        <div class="flex flex-wrap items-center justify-between gap-3 py-3">
                            <span class="font-medium">{{ \Illuminate\Support\Carbon::parse($report['date'])->format('d/m/Y') }}</span>
                            <div class="flex gap-2">
                                @if ($report['pdf'])
                                    <a href="{{ route('admin.reports.daily', ['date' => $report['date'], 'format' => 'pdf']) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium hover:bg-slate-50">PDF</a>
                                @endif
                                @if ($report['csv'])
                                    <a href="{{ route('admin.reports.daily', ['date' => $report['date'], 'format' => 'csv']) }}" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium hover:bg-slate-50">CSV Excel</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="py-3 text-sm text-slate-500">Aucune archive quotidienne pour le moment.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </body>
</html>
