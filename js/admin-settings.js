/**
 * GlobalQuota Admin Settings JavaScript
 * Maneja el gráfico donut y las acciones de administración
 */

(function() {
    'use strict';

    let quotaChart = null;

    function initChart() {
        const canvas = document.getElementById('globalquota-chart');
        if (!canvas) return;

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
                                const percentage = ((context.raw / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });

        loadQuotaData();
    }

    function loadQuotaData() {
        fetch(OC.generateUrl('/apps/globalquota/quota'))
            .then(r => r.json())
            .then(data => {
                if (typeof data.used !== 'undefined' && typeof data.total !== 'undefined') {
                    updateChart(data);
                    updateStats(data);
                } else {
                    showError('Error loading quota data');
                }
            })
            .catch(err => {
                console.error('GlobalQuota error:', err);
                showError('Failed to load quota data');
            });
    }

    function updateChart(data) {
        if (!quotaChart) return;
        const used = data.used || 0;
        const free = data.available || data.free || 0; // soportar ambos nombres

        quotaChart.data.datasets[0].data = [used, free];

        const percentage = data.percentage || 0;
        let usedColor = '#2ecc71';
        if (percentage > 90) usedColor = '#e74c3c';
        else if (percentage > 75) usedColor = '#f39c12';
        else if (percentage > 50) usedColor = '#f1c40f';

        quotaChart.data.datasets[0].backgroundColor = [usedColor, '#ecf0f1'];
        quotaChart.update();
    }

    function updateStats(data) {
        const elems = {
            'quota-used': data.formatted?.used || formatBytes(data.used),
            'quota-free': data.formatted?.free || data.formatted?.available || formatBytes(data.free || data.available),
            'quota-total': data.formatted?.total || formatBytes(data.total),
            'quota-percentage': (data.percentage || 0).toFixed(1) + '%'
        };
        Object.entries(elems).forEach(([id, val]) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        });
    }

    function formatBytes(bytes) {
        if (!bytes) return '0 B';
        const k = 1024; const sizes = ['B','KB','MB','GB','TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return (bytes / Math.pow(k, i)).toFixed(2) + ' ' + sizes[i];
    }

    function showError(msg) {
        OC.Notification.showTemporary(msg, { type: 'error' });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('globalquota-chart')) {
            if (typeof Chart === 'undefined') {
                const s = document.createElement('script');
                s.src = OC.filePath('globalquota', 'js', 'chart.min.js');
                s.onload = initChart;
                document.head.appendChild(s);
            } else {
                initChart();
            }
        }
        const refreshBtn = document.getElementById('refresh-quota');
        if (refreshBtn) refreshBtn.addEventListener('click', loadQuotaData);
    });
})();
