<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Stations</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <div class="mx-auto max-w-6xl px-6 py-10">
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">Station Manager</p>
                    <h1 class="text-3xl font-bold">Gestion des stations</h1>
                </div>
                <a href="{{ route(auth()->user()->dashboardRoute()) }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Retour</a>
            </div>

            <div class="grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
                <section class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                    <h2 class="mb-4 text-xl font-bold">Ajouter une station</h2>
                    <form method="POST" action="{{ route('admin.stations.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Nom</label>
                            <input name="name" type="text" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Localisation</label>
                            <input name="location" type="text" required class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Statut</label>
                            <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 focus:border-emerald-500 focus:outline-none">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-3 font-semibold text-white hover:bg-emerald-700">Enregistrer</button>
                    </form>
                </section>

                <section class="rounded-2xl bg-white p-6 shadow-md ring-1 ring-slate-200">
                    <h2 class="mb-4 text-xl font-bold">Liste des stations</h2>
                    <div class="space-y-3">
                        @foreach ($stations as $station)
                            <div class="rounded-xl bg-slate-50 p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $station->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $station->location }}</p>
                                    </div>
                                    <span class="rounded-full {{ $station->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }} px-2 py-1 text-xs font-semibold">
                                        {{ $station->status === 'active' ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <details class="mt-3">
                                    <summary class="cursor-pointer text-sm font-medium text-emerald-700">Modifier la station</summary>
                                    <form method="POST" action="{{ route('admin.stations.update', $station) }}" class="mt-3 grid gap-3 sm:grid-cols-2">
                                        @csrf
                                        @method('PUT')
                                        <input name="name" value="{{ $station->name }}" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                        <input name="location" value="{{ $station->location }}" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                        <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                            <option value="active" @selected($station->status === 'active')>Active</option>
                                            <option value="inactive" @selected($station->status === 'inactive')>Inactive</option>
                                        </select>
                                        <button type="submit" class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-semibold text-white">Enregistrer</button>
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
