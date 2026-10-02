<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Tableau de bord administrateur</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <div class="min-h-screen">
            <nav class="border-b border-slate-200 bg-white">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">Station Manager</p>
                        <h1 class="text-xl font-bold">{{ auth()->user()->isAdmin() ? 'Tableau de bord administrateur' : 'Espace '.auth()->user()->roleLabel() }}</h1>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                            Déconnexion
                        </button>
                    </form>
                </div>
            </nav>

            <main class="mx-auto max-w-7xl px-6 py-10">
                <section class="grid gap-6 md:grid-cols-4">
                    <div class="rounded-2xl bg-emerald-600 p-6 text-white shadow-lg">
                        <p class="text-sm uppercase tracking-[0.2em] text-emerald-100">Stations</p>
                        <p class="mt-4 text-4xl font-bold">{{ $stations ?? 0 }}</p>
                        <p class="mt-2 text-sm text-emerald-100">Réseau actif</p>
                    </div>
                    <div class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                        <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Produits</p>
                        <p class="mt-4 text-4xl font-bold text-slate-900">{{ $products->count() }}</p>
                        <p class="mt-2 text-sm text-slate-500">Pétroliers</p>
                    </div>
                    <div class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                        <p class="text-sm uppercase tracking-[0.2em] text-slate-500">Stock total</p>
                        <p class="mt-4 text-4xl font-bold text-slate-900">{{ number_format((float) ($totalStock ?? 0), 0, ',', ' ') }} L</p>
                        <p class="mt-2 text-sm text-slate-500">Disponible</p>
                    </div>
                    <div class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                        <p class="text-sm uppercase tracking-[0.2em] text-slate-500">CA du jour</p>
                        <p class="mt-4 text-4xl font-bold text-slate-900">{{ number_format((float) ($todaySales ?? 0), 0, ',', ' ') }} FCFA</p>
                        <p class="mt-2 text-sm text-slate-500">Ventes</p>
                    </div>
                </section>

                <section class="mt-8 grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
                    <div class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                        <h2 class="text-xl font-bold">Gestion du réseau</h2>
                        <div class="mt-6 space-y-4">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium">Stations-service</span>
                                    <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">{{ $stations ?? 0 }} actives</span>
                                </div>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium">Approvisionnements</span>
                                    <span class="rounded-full bg-sky-100 px-2 py-1 text-xs font-semibold text-sky-700">Suivi</span>
                                </div>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium">Ventes journalières</span>
                                    <span class="rounded-full bg-violet-100 px-2 py-1 text-xs font-semibold text-violet-700">Statistiques</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                        <h2 class="text-xl font-bold">Actions rapides</h2>
                        <div class="mt-6 space-y-3">
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.stations.index') }}" class="block rounded-xl bg-slate-900 px-4 py-3 text-center font-medium text-white hover:bg-slate-800">Gérer les stations</a>
                                <a href="{{ route('admin.products.index') }}" class="block rounded-xl border border-slate-200 px-4 py-3 text-center font-medium text-slate-700 hover:bg-slate-50">Gérer les produits</a>
                                <a href="{{ route('admin.stocks.index') }}" class="block rounded-xl border border-slate-200 px-4 py-3 text-center font-medium text-slate-700 hover:bg-slate-50">Initialiser les stocks</a>
                            @endif
                            @if (auth()->user()->isAdmin() || auth()->user()->role === \App\Models\User::ROLE_STATION_MANAGER)
                                <a href="{{ route('admin.supplies.index') }}" class="block rounded-xl border border-slate-200 px-4 py-3 text-center font-medium text-slate-700 hover:bg-slate-50">Enregistrer approvisionnements</a>
                                <a href="{{ route('admin.sales.index') }}" class="block rounded-xl border border-slate-200 px-4 py-3 text-center font-medium text-slate-700 hover:bg-slate-50">Ventes du jour</a>
                            @endif
                            @if (auth()->user()->isAdmin() || auth()->user()->role === \App\Models\User::ROLE_GENERAL_DIRECTOR)
                                <a href="{{ route('admin.reports.index') }}" class="block rounded-xl border border-slate-200 px-4 py-3 text-center font-medium text-slate-700 hover:bg-slate-50">Rapports & export</a>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="mt-8 rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold">Alerte stock faible</h2>
                        <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">{{ $lowStockStocks->count() }} alertes</span>
                    </div>

                    @if ($lowStockStocks->count() > 0)
                        <div class="mt-5 space-y-3">
                            @foreach ($lowStockStocks as $stock)
                                <div class="flex items-center justify-between rounded-xl bg-red-50 p-4">
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $stock->station->name }} · {{ $stock->product->name }}</p>
                                        <p class="text-sm text-slate-500">Stock : {{ $stock->quantity }} / seuil {{ $stock->product->minimum_stock }} {{ $stock->product->unit }}</p>
                                    </div>
                                    <span class="rounded-full bg-red-600 px-3 py-1 text-xs font-semibold text-white">Alerte stock faible</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-5 text-sm text-slate-500">Aucune alerte sur les stocks initialisés.</p>
                    @endif
                </section>
            </main>
        </div>
    </body>
</html>
