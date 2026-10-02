<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Stocks par station</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <main class="mx-auto max-w-6xl px-6 py-10">
            <header class="mb-8 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Station Manager</p>
                    <h1 class="mt-2 text-3xl font-bold">Stocks par station</h1>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Retour</a>
            </header>

            @if (session('success'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div role="alert" class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif

            @if ($legacyStockProducts->isNotEmpty())
                <section class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-5">
                    <h2 class="font-bold text-amber-900">Stocks historiques non attribués</h2>
                    <p class="mt-1 text-sm text-amber-800">Ces anciens totaux sont conservés, mais leur station n’est pas connue. Ne les additionnez pas aux stocks station avant répartition.</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($legacyStockProducts as $product)
                            <span class="rounded-md bg-white px-3 py-2 text-sm font-medium text-amber-900">{{ $product->name }} : {{ number_format((float) $product->stock_quantity, 2, ',', ' ') }} {{ $product->unit }}</span>
                        @endforeach
                    </div>
                </section>
            @endif

            <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
                <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="text-lg font-bold">Saisir le stock d’ouverture</h2>
                    <p class="mt-2 text-sm text-slate-500">Les anciens totaux globaux sont conservés sans attribution. Définissez ici le solde initial de chaque produit dans sa station.</p>
                    <form method="POST" action="{{ route('admin.stocks.store') }}" class="mt-5 space-y-4">
                        @csrf
                        <div>
                            <label for="station_id" class="mb-1 block text-sm font-medium text-slate-700">Station</label>
                            <select id="station_id" name="station_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-600 focus:outline-none">
                                @foreach ($stations as $station)
                                    <option value="{{ $station->id }}">{{ $station->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="product_id" class="mb-1 block text-sm font-medium text-slate-700">Produit</label>
                            <select id="product_id" name="product_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-600 focus:outline-none">
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->unit }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="quantity" class="mb-1 block text-sm font-medium text-slate-700">Quantité initiale</label>
                            <input id="quantity" name="quantity" type="number" min="0" step="0.01" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-600 focus:outline-none">
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-emerald-700 px-4 py-3 font-semibold text-white hover:bg-emerald-800">Enregistrer le stock</button>
                    </form>
                </section>

                <section class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <h2 class="mb-4 text-lg font-bold">Soldes actuels</h2>
                    <div class="divide-y divide-slate-100">
                        @forelse ($stationStocks as $stock)
                            <div class="flex items-center justify-between gap-4 py-4">
                                <div>
                                    <p class="font-semibold">{{ $stock->station->name }} · {{ $stock->product->name }}</p>
                                    <p class="text-sm text-slate-500">Seuil : {{ $stock->product->minimum_stock }} {{ $stock->product->unit }}</p>
                                </div>
                                <span class="font-semibold {{ $stock->isLowStock() ? 'text-red-700' : 'text-emerald-700' }}">{{ number_format((float) $stock->quantity, 2, ',', ' ') }} {{ $stock->product->unit }}</span>
                            </div>
                        @empty
                            <p class="py-4 text-sm text-slate-500">Aucun stock d’ouverture n’a encore été enregistré.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </main>
    </body>
</html>
