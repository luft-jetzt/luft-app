import Chart from 'chart.js';
import $ from 'jquery';

export default class History {
    constructor(element, options) {
        const defaults = {};

        this.settings = {...defaults, ...options};

        this.createChart();
    }

    getTimestamps() {
        let timestampList = [];

        document.querySelectorAll('tr.datetime').forEach(function(timestampRow) {
            const timestamp = timestampRow.dataset.timestamp;

            timestampList.push(timestamp);
        });

        return timestampList.reverse();
    }

    getValues(pollutantIdentifier) {
        let valueList = [];
        let rowCounter = 0;

        $('td.pollution-value.pollutant-' + pollutantIdentifier).each(function() {
            let value = $(this).data('value');

            valueList.push({
                x: rowCounter,
                y: value,
            });

            ++rowCounter;
        });

        return valueList.reverse();
    }

    createDatasets() {
        let datasetList = [];

        const that = this;

        document.querySelectorAll('th.pollutant').forEach(function(pollutantHeadline) {
            const pollutantIdentifier = pollutantHeadline.dataset.pollutantIdentifier;
            const pollutantName = pollutantHeadline.textContent.trim();

            const pollutantColors = {
                pm10: 'red',
                pm25: 'orange',
                o3: 'green',
                no2: 'blue',
                so2: 'yellow',
                co: 'black',
            };

            const dataset = {
                label: pollutantName,
                data: that.getValues(pollutantIdentifier),
                cubicInterpolationMode: 'monotone',
                borderColor: pollutantColors[pollutantIdentifier],
                fill: false,
            };

            datasetList.push(dataset);
        });

        return datasetList;
    }

    createChart() {
        const timestampList = this.getTimestamps();
        const datasetList = this.createDatasets();

        const ctx = document.getElementById('pollutionChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: timestampList,
                datasets: datasetList,
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero:true
                        },
                    }],
                    xAxes: [{
                        ticks: {
                            minRotation: 90,
                            maxRotation: 90,
                            autoSkip: true,
                            maxTicksLimit: 12,
                            callback: function(value, index, values) {
                                return value.split(' ')[1];
                            },
                        },
                    }]
                },
            },
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new History();
});
