/**
 * Dashboard - grafik batang status audit mutu internal.
 *
 * Data grafik disiapkan oleh controller Dashboard (window.DASHBOARD_AUDIT).
 * Memakai Chart.js bawaan tema (assets/global/vendor/chart-js).
 */
(function () {
    'use strict';

    /**
     * Gambar grafik batang pada canvas #grafikAudit.
     */
    function gambarGrafik() {
        var canvas = document.getElementById('grafikAudit');
        var data = window.DASHBOARD_AUDIT;

        if (!canvas || typeof Chart === 'undefined' || !data) {
            return;
        }

        var satuan = data.satuan || 'audit';

        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: data.label,
                datasets: [{
                    label: 'Jumlah audit',
                    data: data.nilai,
                    backgroundColor: data.warna,
                    borderWidth: 0,
                    maxBarThickness: 48
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: false },
                tooltips: {
                    callbacks: {
                        label: function (item) {
                            return ' ' + item.yLabel + ' ' + satuan;
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        gridLines: { display: false },
                        ticks: { fontSize: 12 }
                    }],
                    yAxes: [{
                        ticks: { beginAtZero: true, precision: 0 },
                        gridLines: { color: 'rgba(0, 0, 0, .06)', drawBorder: false }
                    }]
                }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', gambarGrafik);
    } else {
        gambarGrafik();
    }
})();
