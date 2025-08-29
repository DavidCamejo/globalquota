/**
 * GlobalQuota Admin Settings JavaScript
 * Maneja el gráfico donut y las acciones de administración
 */

(function() {
	'use strict';

	console.log('GlobalQuota admin JS loaded ✅');

	let quotaChart = null;

	function initChart() {
		const canvas = document.getElementById('globalquota-chart');
		if (!canvas) {
			console.warn('GlobalQuota: canvas #globalquota-chart no encontrado');
			return;
		}

		try {
			const ctx = canvas.getContext('2d');

			quotaChart = new Chart(ctx, {
				type: 'doughnut',
				data: {
					labels: [t('globalquota', 'Used'), t('globalquota', 'Free')],
					datasets: [{
						data: [0, 100],
						backgroundColor: ['#e74c3c', '#2ecc71'],
						borderWidth: 0,
						cutout: '70%'
					}]
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: {
						legend: {
							position: 'bottom',
							labels: { padding: 20, usePointStyle: true }
						},
						tooltip: {
							callbacks: {
								label: function(context) {
									const label = context.label || '';
									const value = formatBytes(context.raw);
									const total = context.dataset.data.reduce((a, b) => a + b, 0);
									const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : '0.0';
									return `${label}: ${value} (${percentage}%)`;
								}
							}
						}
					}
				}
			});
			console.log('GlobalQuota: Gráfico inicializado correctamente');
		} catch (error) {
			console.error('GlobalQuota: Error al inicializar el gráfico:', error);
			quotaChart = null;
		}
	}

	function loadQuotaData() {
		console.log('GlobalQuota: Cargando datos...');
		const errorEl = document.getElementById('quota-error');
		if (errorEl) {
			errorEl.style.display = 'none';
			errorEl.textContent = '';
		}

		fetch(OC.generateUrl('/apps/globalquota/status')) // usa endpoint nuevo según README
			.then(async r => {
				console.log('GlobalQuota: Respuesta del endpoint /status:', r.status);
				if (!r.ok) {
					const text = await r.text().catch(() => '');
					throw new Error(text || `HTTP ${r.status}`);
				}
				// algunos servidores devuelven 204/empty; manejar
				const text = await r.text();
				if (!text) return {};
				try {
					const parsed = JSON.parse(text);
					console.log('GlobalQuota: Datos recibidos de /status:', parsed);
					return parsed;
				} catch {
					console.warn('GlobalQuota: respuesta no-JSON', text);
					return {};
				}
			})
			.then(data => {
				// fallback al endpoint legacy si no vino data válida
				if (!data || (typeof data.used === 'undefined' && typeof data.total === 'undefined')) {
					console.log('GlobalQuota: Datos de /status no válidos, intentando /quota...');
					return fetch(OC.generateUrl('/apps/globalquota/quota'))
						.then(rr => {
							console.log('GlobalQuota: Respuesta del endpoint /quota:', rr.status);
							return rr.json();
						})
						.then(dd => {
							console.log('GlobalQuota: Datos recibidos de /quota:', dd);
							if (typeof dd.used !== 'undefined' && typeof dd.total !== 'undefined') {
								updateChart(dd);
								updateStats(dd);
							} else {
								console.error('GlobalQuota: Datos de /quota también inválidos');
								showError('Error loading quota data');
							}
						})
						.catch(err => {
							console.error('GlobalQuota error (legacy fallback):', err);
							showError('Failed to load quota data');
						});
				}
				console.log('GlobalQuota: Actualizando UI con datos válidos');
				updateChart(data);
				updateStats(data);
			})
			.catch(err => {
				console.error('GlobalQuota error:', err);
				showError('Failed to load quota data');
			});
	}

	function updateChart(data) {
		if (!quotaChart) return;
		const used = Number(data.used) || 0;
		const free = Number(data.available ?? data.free ?? 0);
		const total = Number(data.total) || (used + free);

		// si total es menor que used+free, corrige
		const safeFree = total >= used ? (total - used) : free;

		quotaChart.data.datasets[0].data = [used, safeFree];

		const percentage = Number(data.percentage);
		let usedColor = '#2ecc71';
		if (!isNaN(percentage)) {
			if (percentage > 90) usedColor = '#e74c3c';
			else if (percentage > 75) usedColor = '#f39c12';
			else if (percentage > 50) usedColor = '#f1c40f';
			else usedColor = '#2ecc71';
		}
		quotaChart.data.datasets[0].backgroundColor = [usedColor, '#ecf0f1'];
		quotaChart.update();
	}

	function updateStats(data) {
		const formatOr = (val, fmt) => (typeof fmt !== 'undefined' && fmt !== null ? fmt : (typeof val !== 'undefined' ? formatBytes(val) : '-'));
		const usedFmt = data.formatted?.used;
		const freeFmt = data.formatted?.free ?? data.formatted?.available;
		const totalFmt = data.formatted?.total;
		const percentage = (typeof data.percentage !== 'undefined' && data.percentage !== null)
			? Number(data.percentage).toFixed(1) + '%'
			: '-';

		const elems = {
			'quota-used': formatOr(data.used, usedFmt),
			'quota-free': formatOr((data.free ?? data.available), freeFmt),
			'quota-total': formatOr(data.total, totalFmt),
			'quota-percentage': percentage
		};
		Object.entries(elems).forEach(([id, val]) => {
			const el = document.getElementById(id);
			if (el) el.textContent = val;
		});
	}

	function formatBytes(bytes) {
		if (!bytes || isNaN(bytes)) return '0 B';
		const k = 1024; const sizes = ['B','KB','MB','GB','TB','PB'];
		const i = Math.floor(Math.log(bytes) / Math.log(k));
		const idx = Math.max(0, Math.min(i, sizes.length - 1));
		const num = bytes / Math.pow(k, idx);
		return num.toFixed(2) + ' ' + sizes[idx];
	}

	function showError(msg) {
		const errorEl = document.getElementById('quota-error');
		if (errorEl) {
			errorEl.style.display = 'block';
			errorEl.textContent = msg;
		}
		try {
			OC.Notification.showTemporary(msg, { type: 'error' });
		} catch (_) { /* ignore */ }
	}

	document.addEventListener('DOMContentLoaded', function() {
		console.log('GlobalQuota: DOM loaded, iniciando...');
		
		// SIEMPRE cargar los datos de texto, independientemente del gráfico
		loadQuotaData();
		
		// Intentar inicializar el gráfico solo si existe el canvas
		if (document.getElementById('globalquota-chart')) {
			console.log('GlobalQuota: Canvas encontrado, intentando cargar Chart.js...');
			// Si Chart no está, ya lo cargamos por CDN en el template.
			if (typeof Chart === 'undefined') {
				console.warn('GlobalQuota: Chart.js no está disponible todavía');
				// Intento de carga local como respaldo (si existe chart.min.js en la app)
				const s = document.createElement('script');
				s.src = OC.filePath('globalquota', 'js', 'chart.min.js');
				s.onload = function() {
					console.log('GlobalQuota: Chart.js cargado localmente');
					initChart();
				};
				s.onerror = function() {
					console.warn('GlobalQuota: No se pudo cargar Chart.js localmente, gráfico no disponible');
				};
				document.head.appendChild(s);
			} else {
				console.log('GlobalQuota: Chart.js ya disponible');
				initChart();
			}
		} else {
			console.warn('GlobalQuota: Canvas #globalquota-chart no encontrado');
		}
		
		const refreshBtn = document.getElementById('refresh-quota');
		if (refreshBtn) {
			refreshBtn.addEventListener('click', function() {
				console.log('GlobalQuota: Botón refresh presionado');
				loadQuotaData();
			});
		}
	});
})();
