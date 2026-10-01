/**
 * Dashboard - grafik batang dokumen mutu per kategori.
 *
 * Data grafik disiapkan oleh controller Dashboard (window.DASHBOARD_GRAFIK).
 * Memakai Chart.js bawaan tema (assets/global/vendor/chart-js).
 */
(function () {
    'use strict';

    /**
     * Gambar grafik batang pada canvas #grafikDokumen.
     */
    function gambarGrafik() {
        var canvas = document.getElementById('grafikDokumen');
        var data = window.DASHBOARD_GRAFIK;

        if (!canvas || typeof Chart === 'undefined' || !data) {
            return;
        }

        new Chart(canvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: data.label,
                datasets: [{
                    label: 'Jumlah dokumen',
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
                            return ' ' + item.yLabel + ' dokumen';
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
