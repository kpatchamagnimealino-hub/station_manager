<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Direction générale</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <div class="min-h-screen">
            <nav class="border-b border-slate-200 bg-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-700">Station Manager</p>
                        <h1 class="text-xl font-bold">Direction générale</h1>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Déconnexion</button>
                    </form>
                </div>
            </nav>

            <main class="mx-auto max-w-7xl px-6 py-10">
                <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wider text-sky-700">Pilotage du réseau</p>
                        <h2 class="mt-2 text-3xl font-bold">Vue direction générale</h2>
                        <p class="mt-2 text-slate-600">Indicateurs consolidés et performance des stations.</p>
                    </div>
                    <a href="{{ route('admin.reports.index') }}" class="rounded-lg bg-sky-800 px-5 py-3 font-semibold text-white hover:bg-sky-900">Consulter les rapports</a>
                </div>

                <section class="grid gap-4 sm:grid-cols-4">
                    <div class="rounded-xl bg-slate-900 p-6 text-white">
                        <p class="text-sm text-slate-300">Stations du réseau</p>
                        <p class="mt-3 text-3xl font-bold">{{ $stations }}</p>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm text-slate-500">Chiffre d’affaires du jour</p>
                        <p class="mt-3 text-3xl font-bold">{{ number_format((float) $todaySales, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm text-slate-500">Stock consolidé</p>
                        <p class="mt-3 text-3xl font-bold">{{ number_format((float) $totalStock, 0, ',', ' ') }} L</p>
                    </div>
                    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <p class="text-sm text-slate-500">Alertes de stock</p>
                        <p class="mt-3 text-3xl font-bold text-red-700">{{ $lowStockStocks->count() }}</p>
                    </div>
                </section>

                <section class="mt-8 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <h3 class="text-lg font-bold">Ventes du jour par station</h3>
                        <span class="text-sm text-slate-500">{{ now()->format('d/m/Y') }}</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($stationPerformance as $station)
                            <div class="grid gap-2 py-4 sm:grid-cols-[1fr_repeat(3,auto)] sm:items-center sm:gap-6">
                                <div>
                                    <p class="font-semibold">{{ $station->name }}</p>
                                    <p class="text-sm text-slate-500">{{ $station->location }}</p>
                                </div>
                                <span class="text-sm text-slate-600">{{ $station->sales_count }} ventes</span>
                                <span class="text-sm text-slate-600">{{ number_format((float) $station->sales_quantity, 0, ',', ' ') }} L</span>
                                <span class="font-semibold">{{ number_format((float) $station->sales_revenue, 0, ',', ' ') }} FCFA</span>
                            </div>
                        @empty
                            <p class="py-4 text-sm text-slate-500">Aucune station enregistrée.</p>
                        @endforelse
                    </div>
                </section>

                <section class="mt-8 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <div class="mb-5 flex items-center justify-between gap-3">
                        <h3 class="text-lg font-bold">Stocks disponibles par station</h3>
                        <span class="text-sm text-slate-500">{{ $stationStocks->count() }} références initialisées</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($stationStocks as $stock)
                            <div class="flex items-center justify-between gap-4 py-3">
                                <div>
                                    <p class="font-semibold">{{ $stock->station->name }} · {{ $stock->product->name }}</p>
                                    <p class="text-sm text-slate-500">Seuil : {{ $stock->product->minimum_stock }} {{ $stock->product->unit }}</p>
                                </div>
                                <span class="font-semibold {{ $stock->isLowStock() ? 'text-red-700' : 'text-emerald-700' }}">{{ number_format((float) $stock->quantity, 2, ',', ' ') }} {{ $stock->product->unit }}</span>
                            </div>
                        @empty
                            <p class="py-4 text-sm text-slate-500">Aucun stock d’ouverture n’a encore été saisi.</p>
                        @endforelse
                    </div>
                </section>

                @if ($products->contains(fn ($product) => $product->stock_quantity > 0))
                    <section class="mt-6 rounded-xl border border-amber-200 bg-amber-50 p-5">
                        <h3 class="font-bold text-amber-900">Stocks historiques non attribués</h3>
                        <p class="mt-1 text-sm text-amber-800">Ces valeurs globales sont conservées, mais ne sont pas incluses dans les soldes par station.</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach ($products->filter(fn ($product) => $product->stock_quantity > 0) as $product)
                                <span class="rounded-md bg-white px-3 py-2 text-sm font-medium text-amber-900">{{ $product->name }} : {{ number_format((float) $product->stock_quantity, 2, ',', ' ') }} {{ $product->unit }}</span>
                            @endforeach
                        </div>
                    </section>
                @endif
            </main>
        </div>
    </body>
</html>
