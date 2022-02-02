import 'select2/dist/css/select2.min.css';
import 'select2/dist/js/select2.min';
import Highcharts from 'highcharts';
import * as ModalHandler from "./_modalHandler";
import {handleFetchErrors} from "./_common";

const dropdownCategory = $('#chart_category_category');
const dropdownGrouping = $('#chart_category_grouping');

Highcharts.setOptions({
    colors: ['#0d233a', '#2f7ed8', '#8bbc21', '#910000', '#1aadce', '#492970', '#f28f43', '#77a1e5', '#c42525', '#a6c96a'],
    plotOptions: {
        line: {
            marker: {
                symbol: 'circle'
            }
        }
    },
});
const chart = new Highcharts.Chart({
    chart: {
        renderTo: 'chart',
        type: 'line',
        marginTop: 50
    },
    credits: {enabled: false},
    title: {text: ''},
    subtitle: {text: ''},
    legend: {enabled: true},
    xAxis: {
        categories: []
    },
    yAxis: {
        title: {text: ''},
        gridLineWidth: 1,
        gridLineDashStyle: 'LongDash',
        labels: {
            step: 2
        }
    },
    tooltip: {
        formatter: function () {
            return '<b>' + Highcharts.numberFormat(this.y, 2, ',', '.')+' Euro</b>';
        }
    },
    series: []
});

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
        dropdownCategory.on('select2:unselect', onSelectChange);
        dropdownGrouping.on('select2:select', onSelectChange);
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

    function renderChart(json)
    {
        if (chart.series.length > 0) {
            for (let i = 0; i <= chart.series.length; i++) {
                chart.series[0].remove(false);
            }
        }

        chart.xAxis[0].setCategories(json.labels, false);

        json.data.forEach(function(series){
            chart.addSeries({
                name: series.name,
                data: series.data
            }, false);
        });

        chart.redraw();
    }

    init();
})();