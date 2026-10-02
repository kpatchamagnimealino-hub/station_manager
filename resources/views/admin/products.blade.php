<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Produits pétroliers</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <div class="mx-auto max-w-6xl px-6 py-10">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">Station Manager</p>
                    <h1 class="text-3xl font-bold">Produits pétroliers</h1>
                </div>
                <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Retour</a>
            </div>

            <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <section class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                    <h2 class="mb-4 text-xl font-bold">Ajouter un produit</h2>
                    <p class="mb-4 text-sm text-slate-500">Après création, initialisez son stock dans chaque station depuis <a href="{{ route('admin.stocks.index') }}" class="font-semibold text-emerald-700 underline">la gestion des stocks</a>.</p>
                    <form method="POST" action="{{ route('admin.products.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Nom</label>
                            <input name="name" type="text" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Unité</label>
                            <input name="unit" type="text" value="litre" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Seuil minimal par station</label>
                            <input name="minimum_stock" type="number" min="0" step="0.01" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Prix unitaire</label>
                            <input name="price" type="number" min="0" step="0.01" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                            <textarea name="description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:outline-none"></textarea>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-3 font-semibold text-white hover:bg-emerald-700">Enregistrer</button>
                    </form>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                    <h2 class="mb-4 text-xl font-bold">Produits disponibles</h2>
                    <div class="space-y-3">
                        @foreach ($products as $product)
                            <div class="rounded-xl bg-slate-50 p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $product->name }}</p>
                                        <p class="text-sm text-slate-500">Seuil d’alerte : {{ $product->minimum_stock }} {{ $product->unit }}</p>
                                        @if ($product->stock_quantity > 0)
                                            <p class="mt-1 text-xs text-amber-700">Stock historique non attribué : {{ number_format((float) $product->stock_quantity, 2, ',', ' ') }} {{ $product->unit }}</p>
                                        @endif
                                    </div>
                                    <span class="rounded-full bg-slate-200 px-2 py-1 text-xs font-semibold text-slate-700">
                                        {{ $product->stationStocks->count() }} stations
                                    </span>
                                </div>
                                <div class="mt-3 space-y-2">
                                    @forelse ($product->stationStocks as $stock)
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-slate-600">{{ $stock->station->name }}</span>
                                            <span class="font-medium {{ $stock->isLowStock() ? 'text-red-700' : 'text-emerald-700' }}">{{ $stock->quantity }} {{ $product->unit }}{{ $stock->isLowStock() ? ' · Alerte' : '' }}</span>
                                        </div>
                                    @empty
                                        <p class="text-sm text-amber-700">Stocks d’ouverture à saisir par station.</p>
                                    @endforelse
                                </div>
                                <details class="mt-4 border-t border-slate-200 pt-3">
                                    <summary class="cursor-pointer text-sm font-medium text-emerald-700">Modifier le produit</summary>
                                    <form method="POST" action="{{ route('admin.products.update', $product) }}" class="mt-3 grid gap-3 sm:grid-cols-2">
                                        @csrf
                                        @method('PUT')
                                        <input name="name" value="{{ $product->name }}" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                        <input name="unit" value="{{ $product->unit }}" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                        <input name="minimum_stock" type="number" min="0" step="0.01" value="{{ $product->minimum_stock }}" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                        <input name="price" type="number" min="0" step="0.01" value="{{ $product->price }}" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                        <textarea name="description" rows="2" class="rounded-lg border border-slate-300 px-3 py-2 text-sm sm:col-span-2">{{ $product->description }}</textarea>
                                        <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white sm:col-span-2">Enregistrer</button>
                                    </form>
                                </details>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>
        </div>
    </body>
</html>
