import Highcharts from 'highcharts';
import {handleFetchErrors} from "./_common";
import * as ModalHandler from "./_modalHandler";

const chartHeight = 200;
const piChartHeight = 300;

(function(){

    function renderMonthlyRemainingChart(categories, data) {
        const config =  {
            credits: {enabled: false},
            legend: {enabled: false},
            tooltip: {
                formatter: function () {
                    return '<b>' + Highcharts.numberFormat(this.y, 2, ',', '.') + ' Euro</b>';
                }
            },
            title: {
                text: ''
            },
            chart: {
                type: 'column',
                height: chartHeight
            },
            xAxis: {
                categories: categories,
                labels: {
                    autoRotation: false
                }
            },
            yAxis: {
                title: false,
                gridLineWidth: 0,
                labels: {
                    enabled: false
                }
            },
            plotOptions: {
                series: {
                    groupPadding: 0,
                }
            },
            series: [{
                data: data,
                color: '#8bbc21',
                negativeColor: '#910000',
                dataLabels: [{
                    enabled: true,
                    formatter: function() {
                        return Highcharts.numberFormat(this.y, 0, ',', '.');
                    },
                    style: {
                        textOutline: 0,
                        color: '#949494'
                    }
                }]
            }]

        };
        Highcharts.chart('series_monthly_remaining', config);
    }

    function renderAccountProgress(categories, seriesPreviousMonth, seriesCurrentMonth) {
        const config = {
            credits: {enabled: false},
            legend: {enabled: false},
            title: {
                text: ''
            },
            chart: {
                type: 'spline',
                height: chartHeight
            },
            xAxis: {
                categories: categories,
                labels: {
                    autoRotation: false
                }
            },
            yAxis: {
                title: false,
                gridLineWidth: 0,
                labels: {
                    formatter: function () {
                        return Highcharts.numberFormat(this.value, 0, ',', '.') + ' Euro';
                    }
                }
            },
            series: [{
                data: seriesPreviousMonth,
                color: '#0d233a',
                name: 'Vormonat',
                marker: {
                    enabled: false,
                    symbol: 'circle'
                }
            }, {
                data: seriesCurrentMonth,
                color: '#2f7ed8',
                name: 'Dieser Monat',
                marker: {
                    enabled: false,
                    symbol: 'circle'
                }
            }],
            tooltip: {
                shared: true,
                formatter: function () {
                    let tooltip = '';
                    this.points.forEach(function(point) {
                        tooltip += '<span style="color: '+point.color+'"><b>' + point.series.name + ': '+Highcharts.numberFormat(point.y, 2, ',', '.') + ' Euro</b></span><br>';
                    });

                    return tooltip;
                },
                useHTML: true
            }
        };

        Highcharts.chart('series_account_progress', config);

    }

    function renderLastMonthOverview(data) {
        const config = {
            credits: {enabled: false},
            legend: {enabled: false},
            title: {
                text: ''
            },
            tooltip: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        formatter: function() {
                            return this.point.name + ' ' + this.point.y + ' EUR';
                        }
                    },
                    enableMouseTracking: false
                }
            },
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                height: piChartHeight
            },
            series: [{
                name: 'Monthly spendings',
                data: data
            }]
        };

        Highcharts.chart('series_last_month_overview', config);
    }

    function renderCurrentMonthOverview(data) {
        const config = {
            credits: {enabled: false},
            legend: {enabled: false},
            title: {
                text: ''
            },
            tooltip: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        formatter: function() {
                            return this.point.name + ' ' + this.point.y + ' EUR';
                        }
                    },
                    enableMouseTracking: false
                }
            },
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                height: piChartHeight
            },
            series: [{
                name: 'Monthly spendings',
                data: data
            }]
        };

        Highcharts.chart('series_current_month_overview', config);
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

    fetch(dashboard_get_last_month_overview)
        .then(handleFetchErrors)
        .then((res) => res.json())
        .then((json) => {
            document.getElementById('series_last_month_overview_loader').remove();

            if (json.success) {
                renderLastMonthOverview(json.data);
            }
        })
        .catch(function(error) {
            ModalHandler.showErrorModal(error);
        });

    fetch(dashboard_get_current_month_overview)
        .then(handleFetchErrors)
        .then((res) => res.json())
        .then((json) => {
            document.getElementById('series_current_month_overview_loader').remove();

            if (json.success) {
                renderCurrentMonthOverview(json.data);
            }
        })
        .catch(function(error) {
            ModalHandler.showErrorModal(error);
        });
})();
