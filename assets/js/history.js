$(document).ready(createChart);

const pollutantColors = {
    pm10: 'red',
    pm25: 'orange',
    o3: 'green',
    no2: 'blue',
    so2: 'yellow',
    co: 'black',
};

function getTimestamps() {
    let timestampList = [];

    $('tr.datetime').each(function() {
        let timestamp = $(this).data('timestamp');

        timestampList.push(timestamp);
    });

    return timestampList.reverse();
}

function getValues(pollutantIdentifier) {
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

function createDatasets() {
    let datasetList = [];

    $('th.pollutant').each(function() {
        let pollutantIdentifier = $(this).data('pollutant-identifier');
        let pollutantName = $(this).text().trim();

        dataset = {
            label: pollutantName,
            data: getValues(pollutantIdentifier),
            cubicInterpolationMode: 'monotone',
            borderColor: pollutantColors[pollutantIdentifier],
            fill: false,
        };

        datasetList.push(dataset);
    });

    return datasetList;
}

function createChart() {
    let timestampList = getTimestamps();
    let datasetList = createDatasets();

    let ctx = document.getElementById('pollutionChart').getContext('2d');
    let myChart = new Chart(ctx, {
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
