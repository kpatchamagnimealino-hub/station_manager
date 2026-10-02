<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Espace responsable de station</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <div class="min-h-screen">
            <nav class="border-b border-slate-200 bg-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">Station Manager</p>
                        <h1 class="text-xl font-bold">Espace responsable de station</h1>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Déconnexion</button>
                    </form>
                </div>
            </nav>

            <main class="mx-auto max-w-7xl px-6 py-10">
                <div class="mb-8">
                    <p class="text-sm font-semibold uppercase tracking-wider text-emerald-700">Opérations quotidiennes</p>
                    <h2 class="mt-2 text-3xl font-bold">Bonjour, {{ auth()->user()->name }}</h2>
                    <p class="mt-2 text-slate-600">Suivez les stocks et enregistrez les mouvements de carburant.</p>
                </div>

                <section class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-xl border-l-4 border-emerald-600 bg-white p-5 shadow-sm">
                        <p class="text-sm text-slate-500">Ventes du jour</p>
                        <p class="mt-2 text-2xl font-bold">{{ number_format((float) $todaySales, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="rounded-xl border-l-4 border-sky-600 bg-white p-5 shadow-sm">
                        <p class="text-sm text-slate-500">Stock disponible</p>
                        <p class="mt-2 text-2xl font-bold">{{ number_format((float) $totalStock, 0, ',', ' ') }} L</p>
                    </div>
                    <div class="rounded-xl border-l-4 border-red-600 bg-white p-5 shadow-sm">
                        <p class="text-sm text-slate-500">Produits en alerte</p>
                        <p class="mt-2 text-2xl font-bold">{{ $lowStockStocks->count() }}</p>
                    </div>
                </section>

                <section class="mt-8 grid gap-6 md:grid-cols-2">
                    <a href="{{ route('admin.sales.index') }}" class="rounded-xl bg-slate-900 p-6 text-white transition hover:bg-slate-800">
                        <p class="text-sm font-medium text-emerald-300">VENTES</p>
                        <h3 class="mt-3 text-xl font-bold">Saisir une vente</h3>
                        <p class="mt-2 text-sm text-slate-300">Enregistrer les ventes journalières et consulter leur historique.</p>
                    </a>
                    <a href="{{ route('admin.supplies.index') }}" class="rounded-xl border border-slate-200 bg-white p-6 transition hover:border-emerald-500">
                        <p class="text-sm font-medium text-emerald-700">STOCK</p>
                        <h3 class="mt-3 text-xl font-bold">Enregistrer un approvisionnement</h3>
                        <p class="mt-2 text-sm text-slate-600">Ajouter une réception de carburant au stock.</p>
                    </a>
                </section>

                <section class="mt-8 rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-lg font-bold">Stocks par station</h3>
                        <span class="text-sm text-slate-500">{{ $stationStocks->count() }} références initialisées</span>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse ($stationStocks as $stock)
                            <div class="flex items-center justify-between gap-4 py-3">
                                <div>
                                    <p class="font-medium">{{ $stock->station->name }} · {{ $stock->product->name }}</p>
                                    <p class="text-sm text-slate-500">Seuil : {{ $stock->product->minimum_stock }} {{ $stock->product->unit }}</p>
                                </div>
                                <span class="font-semibold {{ $stock->isLowStock() ? 'text-red-700' : 'text-emerald-700' }}">{{ $stock->quantity }} {{ $stock->product->unit }}</span>
                            </div>
                        @empty
                            <p class="py-3 text-sm text-slate-500">Aucun stock d’ouverture n’a encore été saisi.</p>
                        @endforelse
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
