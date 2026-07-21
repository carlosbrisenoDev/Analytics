document.addEventListener('DOMContentLoaded', () => {
    const siteSelector = document.getElementById('site-selector') as HTMLSelectElement | null;
    const clientNameEl = document.getElementById('client-name') as HTMLElement | null;
    
    const metricTraffic = document.getElementById('metric-traffic');
    const trendTraffic = document.getElementById('trend-traffic');
    const metricConversion = document.getElementById('metric-conversion');
    const trendConversion = document.getElementById('trend-conversion');
    const metricRetention = document.getElementById('metric-retention');
    const trendRetention = document.getElementById('trend-retention');
    
    const healthScoreMetric = document.getElementById('health-score-metric');
    const healthScoreTrend = document.getElementById('health-score-trend');
    const weekTitle = document.getElementById('week-title');

    const updateTrend = (el: HTMLElement | null, text: string) => {
        if (!el) return;
        el.innerText = text;
        el.classList.remove('bad', 'warn', 'blue');
        if (text.startsWith('-')) {
            el.classList.add('bad');
        } else if (text === '0%' || text === '0') {
            el.classList.add('warn');
        }
    };

    const fetchDashboardData = async (apiKey: string) => {
        if (!apiKey) return;

        try {
            // Fetch overview data
            const overviewRes = await fetch('/api/v1/analytics/dashboard/overview', {
                headers: { 'X-API-KEY': apiKey, 'Accept': 'application/json' }
            });
            
            if (overviewRes.ok) {
                const overview = await overviewRes.json();
                
                if (weekTitle) weekTitle.innerText = `Semana ${overview.week}`;
                if (healthScoreMetric) healthScoreMetric.innerText = overview.health_score;
                if (healthScoreTrend) healthScoreTrend.innerText = `${overview.traffic_trend} tráfico · ${overview.conversion_trend} conversión · ${overview.time_trend} tiempo`;

                if (metricTraffic) metricTraffic.innerText = overview.traffic_total >= 1000 ? (overview.traffic_total / 1000).toFixed(1) + 'K' : overview.traffic_total.toString();
                updateTrend(trendTraffic, overview.traffic_trend);

                if (metricConversion) metricConversion.innerText = overview.conversion_total.toString();
                updateTrend(trendConversion, overview.conversion_trend);

                if (metricRetention) metricRetention.innerText = overview.avg_time;
                updateTrend(trendRetention, overview.time_trend);

                if (overview.pages && overview.total_visits) {
                    const pagesTable = document.getElementById('pages-table');
                    if (pagesTable) {
                        pagesTable.innerHTML = '';
                        Object.entries(overview.pages).forEach(([url, hits]) => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `<td>${url}</td><td>${hits} visitas</td>`;
                            pagesTable.appendChild(tr);
                        });
                    }

                    const funnelContainer = document.getElementById('funnel-container');
                    if (funnelContainer) {
                        funnelContainer.innerHTML = '';
                        Object.entries(overview.pages).forEach(([url, hits]) => {
                            const percentage = Math.round((Number(hits) / overview.total_visits) * 100);
                            const div = document.createElement('div');
                            div.className = 'step';
                            div.innerHTML = `Visita a <span>${url}</span> <strong>${percentage}%</strong>`;
                            funnelContainer.appendChild(div);
                        });
                        
                        // Fake final step for conversion
                        const convPercentage = overview.total_visits > 0 ? Math.round((overview.conversion_total / overview.total_visits) * 100) : 0;
                        const convDiv = document.createElement('div');
                        convDiv.className = 'step';
                        convDiv.innerHTML = `Conversión (Formulario) <strong>${convPercentage}%</strong>`;
                        funnelContainer.appendChild(convDiv);
                    }
                }
                
                if (overview.friction_alerts) {
                    const frictionContainer = document.getElementById('friction-alerts');
                    if (frictionContainer) {
                        frictionContainer.innerHTML = '';
                        overview.friction_alerts.forEach((alert: any) => {
                            const div = document.createElement('div');
                            div.className = 'alert';
                            let color = 'grey';
                            if (alert.type === 'danger') color = 'red';
                            if (alert.type === 'warning') color = 'amber';
                            if (alert.type === 'success') color = 'green';
                            div.innerHTML = `<span class="dot ${color}"></span><p class="small">${alert.message}</p>`;
                            frictionContainer.appendChild(div);
                        });
                    }
                }
            }

            // Fetch AI Insights
            const aiResponse = await fetch('/api/v1/analytics/dashboard/ai-insights', {
                headers: { 'X-API-KEY': apiKey, 'Accept': 'application/json' }
            });

            if (!aiResponse.ok) {
                showComingSoonBanner(document.querySelector('#visibilidad .card:nth-child(2)'));
                showComingSoonBanner(document.querySelector('#sistema .card:nth-child(2)'));
            }
        } catch (error) {
            console.error("Error fetching dashboard data:", error);
        }
    };

    const showComingSoonBanner = (element: Element | null) => {
        if (!element || element.querySelector('.coming-soon-banner')) return;
        const banner = document.createElement('div');
        banner.className = 'coming-soon-banner';
        banner.innerHTML = '<span>Próximamente</span><p>Módulo de IA en desarrollo</p>';
        (element as HTMLElement).style.position = 'relative';
        element.appendChild(banner);
    };

    siteSelector?.addEventListener('change', (e) => {
        const target = e.target as HTMLSelectElement;
        const selectedOption = target.options[target.selectedIndex];
        if (clientNameEl) {
            clientNameEl.innerText = selectedOption.dataset.name || 'Selecciona un sitio';
        }
        fetchDashboardData(target.value);
    });

    if (siteSelector?.value) {
        siteSelector.dispatchEvent(new Event('change'));
    }
});
