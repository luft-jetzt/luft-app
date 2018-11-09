$(document).ready(createChart);

function getTimestamps() {
    var timestampList = [];

    $('tr.datetime').each(function() {
        var timestamp = $(this).data('timestamp');

        timestampList.push(timestamp);
    });

    return timestampList;
}

function getValues(pollutantIdentifier) {
    var valueList = [];

    $('td.pollution-value.pollutant-' + pollutantIdentifier).each(function() {
        var value = $(this).data('value');

        valueList.push(value);
    });

    return valueList;
}

function createDatasets() {
    var datasetList = [];

    $('th.pollutant').each(function() {
        var pollutantIdentifier = $(this).data('pollutant-identifier');
        var pollutantName = $(this).text().trim();
        console.log(pollutantName);

        dataset = {
            label: pollutantName,
            data: getValues(pollutantIdentifier),
        };

        datasetList.push(dataset);
    });

    return datasetList;
}

function createChart() {
    let timestampList = getTimestamps();
    let datasetList = createDatasets();

    var ctx = document.getElementById('pollutionChart').getContext('2d');
    var myChart = new Chart(ctx, {
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
                    }
                }]
            }
        }
    });
}
