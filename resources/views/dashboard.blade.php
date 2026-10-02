<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#142c26">
        <title>Vue générale | Station Manager</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="dashboard-shell" data-dashboard>
            <aside class="sidebar" id="sidebar">
                <a class="brand" href="{{ route('dashboard') }}" aria-label="Station Manager, vue générale">
                    <span class="brand-mark" aria-hidden="true">S</span>
                    <span class="brand-name">Station Manager<span>Gestion du réseau</span></span>
                </a>

                <p class="sidebar-label">Pilotage</p>
                <nav class="navigation" aria-label="Navigation principale">
                    <a class="navigation-link is-active" href="#overview" aria-current="page">
                        <span>Vue générale</span>
                    </a>
                    <a class="navigation-link" href="#stations"><span>Stations</span></a>
                    <a class="navigation-link" href="#stocks"><span>Stocks & cuves</span></a>
                    <a class="navigation-link" href="#activity"><span>Activité récente</span></a>
                    <a class="navigation-link" href="#reports"><span>Rapports</span></a>
                    <a class="navigation-link" href="#alerts">
                        <span>Alertes</span>
                        <span class="navigation-count" aria-label="3 alertes">3</span>
                    </a>
                </nav>

                <div class="sidebar-spacer"></div>

                <div class="network-card" aria-label="État du réseau">
                    <div class="network-card-top"><span class="network-status-dot"></span>Réseau opérationnel</div>
                    <strong>12 <small>/ 12</small></strong>
                    <small>stations suivies</small>
                </div>

                <div class="account-row">
                    <span class="account-avatar" aria-hidden="true">DG</span>
                    <span class="account-copy">
                        <strong>Direction générale</strong>
                        <span>Vue consolidée</span>
                    </span>
                </div>
            </aside>

            <button class="sidebar-scrim" type="button" data-menu-close aria-label="Fermer le menu" hidden></button>

            <div class="main-column">
                <header class="topbar">
                    <button class="mobile-menu-button" type="button" data-menu-toggle aria-label="Ouvrir le menu" aria-controls="sidebar" aria-expanded="false">
                        <span></span><span></span><span></span>
                    </button>
                    <div class="breadcrumb">Réseau national <span>/</span> Tableau de bord</div>
                    <div class="topbar-right">
                        <span class="demo-badge">Données de démonstration</span>
                        <time class="topbar-date" datetime="{{ now()->toDateString() }}">{{ now()->locale('fr')->translatedFormat('l j F Y') }}</time>
                    </div>
                </header>

                <main class="main-content">
                    <section class="page-heading" id="overview" aria-labelledby="page-title">
                        <div>
                            <p class="eyebrow">Direction générale</p>
                            <h1 id="page-title">Vue d’ensemble du réseau</h1>
                            <p class="page-description">Activité commerciale et disponibilité des stocks.</p>
                        </div>
                        <label class="period-control" for="sales-period">
                            Période des ventes
                            <select id="sales-period" name="period">
                                <option value="week">7 derniers jours</option>
                                <option value="month">30 derniers jours</option>
                            </select>
                        </label>
                    </section>

                    <section class="metrics-grid" aria-label="Indicateurs clés">
                        <article class="metric-card">
                            <div class="metric-topline"><span class="metric-label">Stations actives</span><span class="metric-mark"></span></div>
                            <strong class="metric-value">12 <small>/ 12</small></strong>
                            <div class="metric-foot"><span class="metric-change">100 %</span><span>du réseau connecté</span></div>
                        </article>
                        <article class="metric-card">
                            <div class="metric-topline"><span class="metric-label">Stock disponible</span><span class="metric-mark"></span></div>
                            <strong class="metric-value">824 600 <small>L</small></strong>
                            <div class="metric-foot"><span class="metric-change">68 %</span><span>de la capacité totale</span></div>
                        </article>
                        <article class="metric-card">
                            <div class="metric-topline"><span class="metric-label">Chiffre d’affaires du jour</span><span class="metric-mark"></span></div>
                            <strong class="metric-value">4,86 <small>M FCFA</small></strong>
                            <div class="metric-foot"><span class="metric-change">+8,2 %</span><span>par rapport à hier</span></div>
                        </article>
                        <article class="metric-card">
                            <div class="metric-topline"><span class="metric-label">Chiffre d’affaires du mois</span><span class="metric-mark"></span></div>
                            <strong class="metric-value">128,5 <small>M FCFA</small></strong>
                            <div class="metric-foot"><span class="metric-change">+5,6 %</span><span>par rapport au mois dernier</span></div>
                        </article>
                    </section>

                    <section class="content-section analytics-grid" id="reports" aria-label="Analyses des ventes">
                        <article class="panel">
                            <header class="panel-header">
                                <div class="panel-heading">
                                    <h2>Évolution du chiffre d’affaires</h2>
                                    <p>Ventes consolidées sur la période sélectionnée</p>
                                </div>
                                <div class="chart-summary">
                                    <span>Total période</span>
                                    <strong data-period-total>31,4 M FCFA</strong>
                                </div>
                            </header>
                            <div class="chart-body" id="sales-chart" role="img" aria-label="Graphique de l’évolution du chiffre d’affaires"></div>
                        </article>

                        <article class="panel" id="products">
                            <header class="panel-header">
                                <div class="panel-heading">
                                    <h2>Ventes par produit</h2>
                                    <p>Répartition des volumes</p>
                                </div>
                            </header>
                            <div class="product-panel-body">
                                <div id="product-chart" role="img" aria-label="Répartition des ventes par produit"></div>
                                <div class="product-legend" aria-label="Détail par produit">
                                    <div class="legend-row"><span class="legend-swatch"></span><span>Super</span><strong>42 %</strong></div>
                                    <div class="legend-row"><span class="legend-swatch"></span><span>Gasoil</span><strong>34 %</strong></div>
                                    <div class="legend-row"><span class="legend-swatch"></span><span>Pétrole</span><strong>16 %</strong></div>
                                    <div class="legend-row"><span class="legend-swatch"></span><span>Gaz</span><strong>8 %</strong></div>
                                </div>
                            </div>
                        </article>
                    </section>

                    <section class="content-section insight-grid" id="stations" aria-label="Performance et alertes des stations">
                        <article class="panel">
                            <header class="panel-header">
                                <div class="panel-heading">
                                    <h2>Ventes par station</h2>
                                    <p>Chiffre d’affaires, 30 derniers jours</p>
                                </div>
                                <div class="chart-summary"><span>Top station</span><strong>Bonanjo</strong></div>
                            </header>
                            <div class="station-chart-body" id="station-chart" role="img" aria-label="Classement du chiffre d’affaires par station"></div>
                        </article>

                        <article class="panel alerts-panel" id="alerts">
                            <header class="panel-header">
                                <div class="panel-heading">
                                    <h2>Alertes de stock</h2>
                                    <p>Stations sous le seuil défini</p>
                                </div>
                                <span class="alerts-count" aria-label="3 alertes actives">3</span>
                            </header>
                            <div class="alert-list">
                                <div class="alert-row">
                                    <span class="alert-indicator"></span>
                                    <div class="alert-copy"><strong>Biyem-Assi</strong><span>Gasoil · rupture de stock</span></div>
                                    <span class="alert-tag">Rupture</span>
                                </div>
                                <div class="alert-row">
                                    <span class="alert-indicator"></span>
                                    <div class="alert-copy"><strong>Bastos</strong><span>Super · 1 840 L disponibles</span></div>
                                    <span class="alert-tag">Critique</span>
                                </div>
                                <div class="alert-row is-warning">
                                    <span class="alert-indicator"></span>
                                    <div class="alert-copy"><strong>Makepe</strong><span>Pétrole · 3 250 L disponibles</span></div>
                                    <span class="alert-tag">À surveiller</span>
                                </div>
                            </div>
                        </article>
                    </section>

                    <section class="content-section operations-grid" id="stocks" aria-label="État des opérations">
                        <article class="panel">
                            <header class="panel-header">
                                <div class="panel-heading">
                                    <h2>Niveau des stocks</h2>
                                    <p>Stations nécessitant un suivi</p>
                                </div>
                                <span class="chart-summary"><span>Seuil réseau</span><strong>20 %</strong></span>
                            </header>
                            <div class="table-wrap">
                                <table class="data-table">
                                    <thead>
                                        <tr><th>Station</th><th>Produit</th><th>Disponible</th><th>Niveau</th><th>État</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Biyem-Assi</td><td>Gasoil</td><td>0 L</td>
                                            <td><div class="stock-level"><span class="stock-track"><span class="stock-fill is-critical" style="width: 0%"></span></span>0 %</div></td>
                                            <td><span class="status-pill is-critical">Rupture</span></td>
                                        </tr>
                                        <tr>
                                            <td>Bastos</td><td>Super</td><td>1 840 L</td>
                                            <td><div class="stock-level"><span class="stock-track"><span class="stock-fill is-critical" style="width: 12%"></span></span>12 %</div></td>
                                            <td><span class="status-pill is-critical">Critique</span></td>
                                        </tr>
                                        <tr>
                                            <td>Makepe</td><td>Pétrole</td><td>3 250 L</td>
                                            <td><div class="stock-level"><span class="stock-track"><span class="stock-fill is-low" style="width: 18%"></span></span>18 %</div></td>
                                            <td><span class="status-pill is-warning">À surveiller</span></td>
                                        </tr>
                                        <tr>
                                            <td>Bonanjo</td><td>Super</td><td>18 600 L</td>
                                            <td><div class="stock-level"><span class="stock-track"><span class="stock-fill" style="width: 74%"></span></span>74 %</div></td>
                                            <td><span class="status-pill">Normal</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </article>

                        <article class="panel" id="activity">
                            <header class="panel-header">
                                <div class="panel-heading">
                                    <h2>Dernières opérations</h2>
                                    <p>Approvisionnements et ventes enregistrés</p>
                                </div>
                            </header>
                            <div class="table-wrap">
                                <table class="data-table">
                                    <thead>
                                        <tr><th>Opération</th><th>Station</th><th>Volume</th><th>Montant</th></tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="operation-name">Vente · Super</span><span class="operation-detail">Aujourd’hui, 10:42</span></td>
                                            <td>Bonanjo</td><td>186 L</td><td>167 400 FCFA</td>
                                        </tr>
                                        <tr>
                                            <td><span class="operation-name">Approvisionnement · Gasoil</span><span class="operation-detail">Aujourd’hui, 09:18</span></td>
                                            <td>Akwa</td><td>8 000 L</td><td>5 840 000 FCFA</td>
                                        </tr>
                                        <tr>
                                            <td><span class="operation-name">Vente · Gasoil</span><span class="operation-detail">Aujourd’hui, 08:56</span></td>
                                            <td>Bastos</td><td>92 L</td><td>82 800 FCFA</td>
                                        </tr>
                                        <tr>
                                            <td><span class="operation-name">Vente · Pétrole</span><span class="operation-detail">Aujourd’hui, 08:31</span></td>
                                            <td>Makepe</td><td>54 L</td><td>40 500 FCFA</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </article>
                    </section>

                    <footer class="page-footer">
                        <span>Station Manager · Vue consolidée du réseau</span>
                        <span>Les chiffres affichés sont des données de démonstration.</span>
                    </footer>
                </main>
            </div>
        </div>
    </body>
</html>
