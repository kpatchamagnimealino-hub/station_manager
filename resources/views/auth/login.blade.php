<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Connexion | Station Manager</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <main class="flex min-h-screen items-center justify-center px-5 py-10">
            <div class="w-full max-w-5xl overflow-hidden rounded-2xl bg-white shadow-xl ring-1 ring-slate-200 lg:grid lg:grid-cols-[0.85fr_1.15fr]">
                <section class="flex flex-col justify-between bg-slate-900 p-8 text-white sm:p-10">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-emerald-400">Station Manager</p>
                        <h1 class="mt-12 max-w-sm text-4xl font-bold leading-tight">Votre réseau, sous contrôle.</h1>
                        <p class="mt-4 max-w-sm text-sm leading-6 text-slate-300">Connectez-vous à votre espace selon votre fonction.</p>
                    </div>
                    <p class="mt-12 text-xs text-slate-400">Gestion centralisée des stations-service</p>
                </section>

                <section class="p-6 sm:p-10">
                    <div class="mb-7">
                        <h2 class="text-2xl font-bold">Connexion</h2>
                        <p class="mt-1 text-sm text-slate-500">Sélectionnez votre profil pour continuer.</p>
                    </div>

                    @if ($errors->any())
                        <div role="alert" class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                        @csrf

                        <fieldset>
                            <legend class="mb-3 text-sm font-medium text-slate-700">Profil</legend>
                            <div class="grid gap-3 sm:grid-cols-3">
                                @foreach ([
                                    'administrator' => ['Administrateur', 'Contrôle complet'],
                                    'station_manager' => ['Responsable', 'Opérations station'],
                                    'general_director' => ['Directeur général', 'Vue et rapports'],
                                ] as $role => [$label, $description])
                                    <label class="cursor-pointer">
                                        <input class="peer sr-only" type="radio" name="role" value="{{ $role }}" @checked(old('role', 'administrator') === $role) required>
                                        <span class="flex h-full min-h-24 flex-col justify-center rounded-xl border border-slate-200 p-3 transition hover:border-emerald-400 peer-checked:border-emerald-600 peer-checked:bg-emerald-50 peer-checked:ring-1 peer-checked:ring-emerald-600 peer-focus-visible:outline-none peer-focus-visible:ring-2 peer-focus-visible:ring-emerald-500">
                                            <span class="text-sm font-semibold text-slate-900">{{ $label }}</span>
                                            <span class="mt-1 text-xs leading-4 text-slate-500">{{ $description }}</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Adresse e-mail</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 outline-none transition focus:border-emerald-600 focus:bg-white">
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Mot de passe</label>
                            <input id="password" name="password" type="password" autocomplete="current-password" required class="w-full rounded-lg border border-slate-300 bg-slate-50 px-4 py-3 outline-none transition focus:border-emerald-600 focus:bg-white">
                        </div>

                        <button type="submit" class="w-full rounded-lg bg-emerald-700 px-4 py-3 font-semibold text-white transition hover:bg-emerald-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2">
                            Se connecter
                        </button>
                    </form>
                </section>
            </div>
        </main>
    </body>
</html>
