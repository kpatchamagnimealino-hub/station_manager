<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Ventes journalières</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <div class="mx-auto max-w-6xl px-6 py-10">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">Station Manager</p>
                    <h1 class="text-3xl font-bold">Ventes journalières</h1>
                </div>
                <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Retour</a>
            </div>

            <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <section class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                    <h2 class="mb-4 text-xl font-bold">Enregistrer une vente</h2>
                    <form method="POST" action="{{ route('admin.sales.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Station</label>
                            <select name="station_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:outline-none">
                                @foreach ($stations as $station)
                                    <option value="{{ $station->id }}">{{ $station->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Produit</label>
                            <select name="product_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:outline-none">
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Quantité</label>
                                <input name="quantity" type="number" min="1" step="0.01" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Prix unitaire</label>
                                <input name="unit_price" type="number" min="0" step="0.01" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Date de vente</label>
                            <input name="sold_at" type="datetime-local" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:outline-none">
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-3 font-semibold text-white hover:bg-emerald-700">Enregistrer la vente</button>
                    </form>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                    <h2 class="mb-4 text-xl font-bold">Historique</h2>
                    <div class="space-y-3">
                        @foreach ($sales as $sale)
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="font-semibold text-slate-800">{{ $sale->product->name }} · {{ $sale->station->name }}</p>
                                <p class="text-sm text-slate-500">{{ $sale->quantity }} unités · {{ $sale->sold_at?->format('d/m/Y H:i') }} · {{ number_format((float) ($sale->quantity * $sale->unit_price), 0, ',', ' ') }} FCFA</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <section class="mt-6 rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                <h2 class="mb-4 text-xl font-bold">Stocks disponibles par station</h2>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($stationStocks as $stock)
                        <div class="rounded-lg bg-slate-50 p-4">
                            <p class="font-semibold">{{ $stock->station->name }} · {{ $stock->product->name }}</p>
                            <p class="mt-1 text-sm {{ $stock->isLowStock() ? 'text-red-700' : 'text-slate-600' }}">{{ $stock->quantity }} {{ $stock->product->unit }}{{ $stock->isLowStock() ? ' · Stock faible' : '' }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Aucun stock d’ouverture saisi.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </body>
</html>
