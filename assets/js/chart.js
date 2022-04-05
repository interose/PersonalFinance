import 'select2/dist/js/select2.min';
import 'select2/dist/css/select2.min.css';
import 'bootstrap-toggle';
import 'bootstrap-toggle/css/bootstrap-toggle.min.css';
import '../css/chart.css';

import Highcharts from 'highcharts';
import * as ModalHandler from "./_modalHandler";
import {handleFetchErrors} from "./_common";

const dropdownCategory = $('#chart_category_category');
const checkbox = $('#chart_category_drilldown');
const checkboxJs = document.getElementById('chart_category_drilldown');


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
            maximumSelectionLength: 5,
            minimumResultsForSearch: -1
        });
        checkbox.bootstrapToggle({
            size: 'small',
            on: translation.toggleYes,
            off: translation.toggleNo,
            onstyle: 'my-toggle',
            width: 60
        });

        // Init event listeners
        dropdownCategory.on('select2:select', onSelectChange);
        dropdownCategory.on('select2:unselect', onSelectChange);
        checkbox.on('change', onCheckboxChange);
    }

    function onSelectChange(e) {
        checkboxJs.dataset.handleEvent = 'false';
        checkbox.bootstrapToggle('off');
        checkboxJs.dataset.handleEvent = 'true';
        fetchData();
    }

    function onCheckboxChange(e) {
        if (checkboxJs.dataset.handleEvent === "true") {
            fetchData();
        }
    }

    function fetchData() {
        fetch(chart_data + '?' + new URLSearchParams({
            categoryGroupId: dropdownCategory.val(),
            splitIntoCategories: checkbox.prop('checked'),
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

    function renderChart(json) {
        while (chart.series.length) {
            chart.series[0].remove();
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