/**
 * GlobalQuota Admin Settings JavaScript
 * Maneja el gráfico donut y las acciones de administración
 */

(function() {
    'use strict';

    // Validate Nextcloud environment
    if (typeof OC === 'undefined' || typeof t === 'undefined') {
        console.error('GlobalQuota: Nextcloud environment not available');
        return;
    }

    console.log('GlobalQuota admin JS loaded ✅');

    let quotaChart = null;
    let chartInitialized = false;
    let dataLoaded = false;

    function initChart() {
        const canvas = document.getElementById('globalquota-chart');
        if (!canvas) {
            console.warn('GlobalQuota: canvas #globalquota-chart no encontrado');
            return;
        }

        if (typeof Chart === 'undefined') {
            console.warn('GlobalQuota: Chart.js no está disponible');
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
                        backgroundColor: ['#e74c3c', '#ecf0f1'],
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
                            labels: { 
                                padding: 20, 
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
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

            chartInitialized = true;
            console.log('GlobalQuota: Gráfico inicializado correctamente');
            
        } catch (error) {
            console.error('GlobalQuota: Error al inicializar el gráfico:', error);
            quotaChart = null;
            chartInitialized = false;
        }
    }

    function loadQuotaData() {
        // Prevent multiple simultaneous requests
        if (dataLoaded) {
            console.log('GlobalQuota: Data already loading, skipping...');
            return;
        }
        
        dataLoaded = true;
        console.log('GlobalQuota: Cargando datos...');
        
        // Clear any previous errors
        const errorEl = document.getElementById('quota-error');
        if (errorEl) {
            errorEl.style.display = 'none';
            errorEl.textContent = '';
        }

        // Try the new /status endpoint first
        fetch(OC.generateUrl('/apps/globalquota/status'))
            .then(async response => {
                console.log('GlobalQuota: Respuesta del endpoint /status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                
                const text = await response.text();
                if (!text) {
                    throw new Error('Empty response');
                }
                
                try {
                    const data = JSON.parse(text);
                    console.log('GlobalQuota: Datos recibidos de /status:', data);
                    return data;
                } catch (parseError) {
                    console.warn('GlobalQuota: respuesta no-JSON', text);
                    throw new Error('Invalid JSON response');
                }
            })
            .then(data => {
                if (data && typeof data.used !== 'undefined' && typeof data.total !== 'undefined') {
                    processQuotaData(data);
                } else {
                    throw new Error('Invalid data structure');
                }
            })
            .catch(error => {
                console.log('GlobalQuota: /status failed, trying /quota endpoint...', error.message);
                
                // Fallback to legacy /quota endpoint
                return fetch(OC.generateUrl('/apps/globalquota/quota'))
                    .then(response => {
                        console.log('GlobalQuota: Respuesta del endpoint /quota:', response.status);
                        
                        if (!response.ok) {
                            throw new Error(`HTTP ${response.status}`);
                        }
                        
                        return response.json();
                    })
                    .then(data => {
                        console.log('GlobalQuota: Datos recibidos de /quota:', data);
                        
                        if (data && typeof data.used !== 'undefined' && typeof data.total !== 'undefined') {
                            processQuotaData(data);
                        } else {
                            throw new Error('Invalid quota data structure');
                        }
                    })
                    .catch(fallbackError => {
                        console.error('GlobalQuota: Both endpoints failed:', fallbackError);
                        showError('Failed to load quota data from both endpoints');
                    });
            })
            .finally(() => {
                dataLoaded = false;
            });
    }

    function processQuotaData(data) {
        // Unified calculation logic for both chart and stats
        const used = Number(data.used) || 0;
        const total = Number(data.total) || 0;
        const available = Number(data.available || data.free || 0);
        
        // Calculate free space with unified logic
        let free;
        if (total > 0) {
            // If we have total, calculate free as total - used
            free = Math.max(0, total - used);
        } else if (available > 0) {
            // If no total but we have available, use that
            free = available;
        } else {
            // Fallback: assume no free space if we can't calculate
            free = 0;
        }

        // Calculate percentage
        const percentage = total > 0 ? ((used / total) * 100) : 0;

        // Create normalized data object
        const normalizedData = {
            used: used,
            free: free,
            total: total > 0 ? total : (used + free),
            percentage: percentage,
            formatted: data.formatted || {}
        };

        console.log('GlobalQuota: Normalized data:', normalizedData);

        // Update both chart and stats with the same data
        updateChart(normalizedData);
        updateStats(normalizedData);
    }

    function updateChart(data) {
        if (!quotaChart || !chartInitialized) {
            console.warn('GlobalQuota: Chart not initialized, skipping update');
            return;
        }

        console.log('GlobalQuota: Updating chart with:', { 
            used: data.used, 
            free: data.free, 
            total: data.total 
        });

        quotaChart.data.datasets[0].data = [data.used, data.free];

        // Update colors based on usage percentage
        let usedColor = '#2ecc71'; // Green
        
        if (data.percentage > 90) {
            usedColor = '#e74c3c'; // Red
        } else if (data.percentage > 75) {
            usedColor = '#f39c12'; // Orange
        } else if (data.percentage > 50) {
            usedColor = '#f1c40f'; // Yellow
        }

        quotaChart.data.datasets[0].backgroundColor = [usedColor, '#ecf0f1'];
        quotaChart.update('active');
    }

    function updateStats(data) {
        // Use the same normalized data for stats
        const elems = {
            'quota-used': data.formatted?.used || formatBytes(data.used),
            'quota-free': data.formatted?.free || data.formatted?.available || formatBytes(data.free),
            'quota-total': data.formatted?.total || formatBytes(data.total),
            'quota-percentage': data.percentage.toFixed(1) + '%'
        };

        console.log('GlobalQuota: Updating stats with:', elems);

        Object.entries(elems).forEach(([id, val]) => {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = val;
            } else {
                console.warn(`GlobalQuota: Element #${id} not found`);
            }
        });
    }

    function formatBytes(bytes) {
        if (!bytes || isNaN(bytes) || bytes === 0) return '0 B';
        
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        const idx = Math.max(0, Math.min(i, sizes.length - 1));
        const num = bytes / Math.pow(k, idx);
        
        return num.toFixed(2) + ' ' + sizes[idx];
    }

    function showError(msg) {
        console.error('GlobalQuota:', msg);
        
        // Try to show error in dedicated error element
        const errorEl = document.getElementById('quota-error');
        if (errorEl) {
            errorEl.style.display = 'block';
            errorEl.textContent = msg;
        }
        
        // Also try Nextcloud notification system
        try {
            if (OC && OC.Notification) {
                OC.Notification.showTemporary(msg, { type: 'error' });
            }
        } catch (e) {
            console.warn('GlobalQuota: Could not show notification:', e);
        }
    }

    function loadChartJS() {
        return new Promise((resolve, reject) => {
            if (typeof Chart !== 'undefined') {
                console.log('GlobalQuota: Chart.js already available');
                resolve();
                return;
            }

            console.log('GlobalQuota: Loading Chart.js...');
            
            // Try to load from CDN first
            const cdnScript = document.createElement('script');
            cdnScript.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js';
            cdnScript.onload = function() {
                console.log('GlobalQuota: Chart.js loaded from CDN');
                resolve();
            };
            cdnScript.onerror = function() {
                console.warn('GlobalQuota: CDN failed, trying local Chart.js...');
                
                // Fallback to local file
                const localScript = document.createElement('script');
                localScript.src = OC.filePath('globalquota', 'js', 'chart.min.js');
                localScript.onload = function() {
                    console.log('GlobalQuota: Chart.js loaded locally');
                    resolve();
                };
                localScript.onerror = function() {
                    console.error('GlobalQuota: Failed to load Chart.js from both CDN and local');
                    reject(new Error('Chart.js not available'));
                };
                document.head.appendChild(localScript);
            };
            document.head.appendChild(cdnScript);
        });
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('GlobalQuota: DOM loaded, iniciando...');
        
        // Initialize chart if canvas exists
        const canvas = document.getElementById('globalquota-chart');
        if (canvas) {
            console.log('GlobalQuota: Canvas found, loading Chart.js...');
            
            loadChartJS()
                .then(() => {
                    initChart();
                    // Load data only after chart is ready
                    loadQuotaData();
                })
                .catch(error => {
                    console.error('GlobalQuota: Failed to initialize chart:', error);
                    showError('Chart visualization not available');
                    // Still load data for stats even if chart fails
                    loadQuotaData();
                });
        } else {
            console.warn('GlobalQuota: Canvas #globalquota-chart not found');
            // Load data for stats only
            loadQuotaData();
        }
        
        // Set up refresh button
        const refreshBtn = document.getElementById('refresh-quota');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                console.log('GlobalQuota: Refresh button clicked');
                loadQuotaData();
            });
        }
    });
})();