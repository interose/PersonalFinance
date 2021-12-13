import 'select2/dist/css/select2.min.css';
import 'select2/dist/js/select2.min';
import Chart from "chart.js/auto";

const chartContainer = document.getElementById('chart').getContext('2d');
let chart = undefined;
const dropdownCategory = $('#chart_category_category');
const dropdownGrouping = $('#chart_category_grouping');
const chartColors = ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'];

import * as ModalHandler from "./_modalHandler";
import {handleFetchErrors} from "./_common";

(function(){
    function init() {
        dropdownCategory.select2({
            width: '100%',
            maximumSelectionLength: 5
        });
        dropdownGrouping.select2({
            minimumResultsForSearch: Infinity,
        });

        // Init event listeners
        dropdownCategory.on('select2:select', onSelectChange);
        dropdownGrouping.on('select2:select', onSelectChange);
    }

    function renderChart(response) {
        if (chart !== undefined) {
            chart.destroy();
        }

        const config = {
            type: 'line',
            data: {
                labels: response.labels,
                datasets: []
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
                            // title(tooltipItems) {
                            //     return '';
                            // }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false,
                            drawOnChartArea: true,
                            drawTicks: false,
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        grid: {
                            drawBorder: false,
                            color: function(context) {
                                return '#d4d4d4';
                            }
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
        };

        response.data.forEach(function(serie, index){
            config.data.datasets.push({
                label: serie.name,
                data: serie.data,
                borderWidth: 3,
                pointRadius: 0,
                pointHoverRadius: 2,
                // tension: 0.3,
                fill: false,
                borderColor: chartColors[index],
                backgroundColor: chartColors[index]
            });
        });

        chart = new Chart(chartContainer, config);
    }

    function onSelectChange(e) {
        fetch(chart_data + '?' + new URLSearchParams({
                grouping: dropdownGrouping.val(),
                categories: dropdownCategory.val().join()
            }))
            .then(handleFetchErrors)
            .then((res) => res.json())
            .then((json) => {
                renderChart(json);
            })
            .catch(function(error) {
                ModalHandler.showErrorModal(error);
            });
    }

    init();
})();
