import Chart from 'chart.js/auto';
import {handleFetchErrors} from "./_common";
import * as ModalHandler from "./_modalHandler";

(function(){

    function renderMonthlyRemainingChart(categories, data) {
        const chart_monthly_remaining = document.getElementById('series_monthly_remaining').getContext('2d');
        const colours = data.map((value) => value < 0 ? '#910000' : '#8bbc21');
        const myChart = new Chart(chart_monthly_remaining, {
            type: 'bar',
            data: {
                labels: categories,
                datasets: [{
                    label: 'test',
                    data: data,
                    backgroundColor: colours,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 10
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        displayColors: false,
                        bodyFont: {
                            size: 14
                        },
                        callbacks: {
                            label: function(context) {
                                if (context.parsed.y !== null) {
                                    return new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(context.parsed.y);
                                }
                            },
                            title(tooltipItems) {
                                return '';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value, index, values) {
                                return index % 2 === 0 ? new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR', minimumFractionDigits: 0 }).format(value) : '';
                            },
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }

    function renderAccountProgress(categories, seriesPreviousMonth, seriesCurrentMonth) {
        const chart_account_progress = document.getElementById('series_account_progress').getContext('2d');
        const myChart = new Chart(chart_account_progress, {
            type: 'line',
            data: {
                labels: categories,
                datasets: [{
                    label: 'Vormonat',
                    data: seriesPreviousMonth,
                    borderWidth: 3,
                    pointRadius: 0,
                    pointHoverRadius: 2,
                    tension: 0.3,
                    fill: false,
                    borderColor: '#58508d',
                    backgroundColor: '#58508d'
                }, {
                    label: 'Aktueller Monat',
                    data: seriesCurrentMonth,
                    borderWidth: 3,
                    pointRadius: 0,
                    pointHoverRadius: 2,
                    tension: 0.3,
                    fill: false,
                    borderColor: '#ffa600',
                    backgroundColor: '#ffa600'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 10
                },
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        displayColors: false,
                        bodyFont: {
                            size: 14
                        },
                        callbacks: {
                            label: function(context) {
                                if (context.parsed.y !== null) {
                                    let value = new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR' }).format(context.parsed.y);
                                    return context.dataset.label+': '+value;
                                }
                            },
                            title(tooltipItems) {
                                return '';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value, index, values) {
                                return index % 2 === 0 ? new Intl.NumberFormat('de-DE', { style: 'currency', currency: 'EUR', minimumFractionDigits: 0 }).format(value) : '';
                            },
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }

    fetch(dashboard_get_monthly_remaining)
        .then(handleFetchErrors)
        .then((res) => res.json())
        .then((json) => {
            document.getElementById('series_monthly_remaining_loader').remove();

            if (json.success) {
                renderMonthlyRemainingChart(json.categories, json.data);
            }
        })
        .catch(function(error) {
            ModalHandler.showErrorModal(error);
        });

    fetch(dashboard_get_account_progress)
        .then(handleFetchErrors)
        .then((res) => res.json())
        .then((json) => {
            document.getElementById('series_account_progress_loader').remove();

            if (json.success) {
                renderAccountProgress(json.categories, json.seriesPreviousMonth, json.seriesCurrentMonth);
            }
        })
        .catch(function(error) {
            ModalHandler.showErrorModal(error);
        });
})();
