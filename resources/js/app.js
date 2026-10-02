import ApexCharts from 'apexcharts';

const dashboard = document.querySelector('[data-dashboard]');

if (dashboard) {
	const salesPeriods = {
		week: {
			categories: ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
			values: [3.8, 4.2, 3.6, 4.9, 5.4, 6.1, 3.4],
			total: '31,4 M FCFA',
		},
		month: {
			categories: ['1-5', '6-10', '11-15', '16-20', '21-25', '26-30'],
			values: [18.2, 21.4, 19.8, 24.1, 26.6, 18.4],
			total: '128,5 M FCFA',
		},
	};

	const formatCurrency = (value) => `${value.toLocaleString('fr-FR')} M FCFA`;
	const salesChart = new ApexCharts(document.querySelector('#sales-chart'), {
		chart: {
			type: 'area',
			height: 270,
			toolbar: { show: false },
			zoom: { enabled: false },
			fontFamily: 'Instrument Sans, sans-serif',
			animations: { speed: 500 },
		},
		series: [{ name: 'Chiffre d’affaires', data: salesPeriods.week.values }],
		colors: ['#276c58'],
		stroke: { curve: 'smooth', width: 3 },
		fill: {
			type: 'gradient',
			gradient: { opacityFrom: 0.24, opacityTo: 0.02, stops: [0, 90, 100] },
		},
		dataLabels: { enabled: false },
		grid: { borderColor: '#e8ece6', strokeDashArray: 4, padding: { left: 4, right: 10 } },
		xaxis: {
			categories: salesPeriods.week.categories,
			axisBorder: { show: false },
			axisTicks: { show: false },
			labels: { style: { colors: '#7c8981', fontSize: '12px' } },
		},
		yaxis: {
			min: 0,
			max: 8,
			tickAmount: 4,
			labels: {
				formatter: (value) => `${value.toFixed(0)} M`,
				style: { colors: '#7c8981', fontSize: '12px' },
			},
		},
		tooltip: { y: { formatter: formatCurrency } },
	});

	const productChart = new ApexCharts(document.querySelector('#product-chart'), {
		chart: {
			type: 'donut',
			height: 230,
			fontFamily: 'Instrument Sans, sans-serif',
		},
		series: [42, 34, 16, 8],
		labels: ['Super', 'Gasoil', 'Pétrole', 'Gaz'],
		colors: ['#276c58', '#d3e85a', '#e99068', '#75b6b0'],
		stroke: { width: 3, colors: ['#ffffff'] },
		dataLabels: { enabled: false },
		legend: { show: false },
		plotOptions: {
			pie: {
				donut: {
					size: '76%',
					labels: {
						show: true,
						name: { show: true, offsetY: 18, color: '#7c8981', fontSize: '12px' },
						value: { show: true, offsetY: -14, color: '#182a24', fontSize: '22px', fontWeight: 600 },
						total: {
							show: true,
							label: 'Ventes',
							color: '#7c8981',
							formatter: () => '100 %',
						},
					},
				},
			},
		},
		tooltip: { y: { formatter: (value) => `${value} % du volume vendu` } },
	});

	const stationChart = new ApexCharts(document.querySelector('#station-chart'), {
		chart: {
			type: 'bar',
			height: 250,
			toolbar: { show: false },
			fontFamily: 'Instrument Sans, sans-serif',
		},
		series: [{ name: 'CA', data: [28.4, 22.1, 18.6, 14.2, 11.8] }],
		colors: ['#276c58'],
		plotOptions: {
			bar: {
				horizontal: true,
				borderRadius: 4,
				barHeight: '48%',
				distributed: true,
			},
		},
		dataLabels: { enabled: false },
		xaxis: {
			categories: ['Bonanjo', 'Bastos', 'Makepe', 'Biyem-Assi', 'Akwa'],
			max: 32,
			labels: {
				formatter: (value) => `${Number(value).toFixed(0)} M`,
				style: { colors: '#7c8981', fontSize: '11px' },
			},
		},
		yaxis: { labels: { style: { colors: '#4d5e55', fontSize: '12px' } } },
		grid: { borderColor: '#e8ece6', strokeDashArray: 4, padding: { left: 8, right: 8 } },
		tooltip: { y: { formatter: formatCurrency } },
		legend: { show: false },
	});

	salesChart.render();
	productChart.render();
	stationChart.render();

	const periodSelect = dashboard.querySelector('#sales-period');
	const periodTotal = dashboard.querySelector('[data-period-total]');

	periodSelect.addEventListener('change', (event) => {
		const period = salesPeriods[event.target.value];

		periodTotal.textContent = period.total;
		salesChart.updateOptions({ xaxis: { categories: period.categories } });
		salesChart.updateSeries([{ name: 'Chiffre d’affaires', data: period.values }]);
	});

	const menuToggle = dashboard.querySelector('[data-menu-toggle]');
	const menuClose = dashboard.querySelector('[data-menu-close]');

	const setMenuOpen = (isOpen) => {
		dashboard.classList.toggle('menu-open', isOpen);
		menuToggle.setAttribute('aria-expanded', String(isOpen));
	};

	menuToggle.addEventListener('click', () => {
		setMenuOpen(!dashboard.classList.contains('menu-open'));
	});
	menuClose.addEventListener('click', () => setMenuOpen(false));
	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			setMenuOpen(false);
		}
	});

	dashboard.querySelectorAll('.navigation-link').forEach((link) => {
		link.addEventListener('click', () => setMenuOpen(false));
	});
}
