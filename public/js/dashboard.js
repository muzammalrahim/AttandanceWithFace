$(function (){
    // department stacked bar chart
    var ticksStyle = {
        fontColor: '#495057',
        fontStyle: 'bold'
    }

    var mode = 'index';
    var intersect = true;

    var $departmentStackedChart = $('#department-stacked-chart');
    var departmentStackedChart = new Chart($departmentStackedChart, {
        type: 'bar',
        data: {
            labels: departmentNamesForBarChart,
            datasets: [
                {
                    backgroundColor: '#1da10d',
                    borderColor: '#1da10d',
                    data: totalDepartmentPresentEmployees,
                },
                {
                    backgroundColor: '#ced4da',
                    borderColor: '#ced4da',
                    data: totalDepartmentTimedOutEmployees,
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                mode: mode,
                intersect: intersect
            },
            hover: {
                mode: mode,
                intersect: intersect
            },
            legend: {
                display: false
            },
            scales: {
                yAxes: [{
                    stacked: true,
                    display: true,
                    gridLines: {
                        display: true,
                        lineWidth: '4px',
                        color: 'rgba(0, 0, 0, .2)',
                        zeroLineColor: 'transparent'
                    },
                    ticks: {
                        min: 0,
                        max: totalAttendee,
                        stepSize: 1
                    }
                }],
                xAxes: [{
                    display: true,
                    gridLines: {
                        display: false
                    },
                    ticks: ticksStyle,
                    stacked: true
                }]
            }
        }
    });

    // department donught chart
    var $departmentDoughnutChart = $('#department-donought-chart');
    var departmentDoughnutChart = new Chart($departmentDoughnutChart, {
        type: 'doughnut',
        data: {
            labels: departmentNamesForBarChart,
            datasets: [{
                data: [10,20,50,40,30],
                backgroundColor: ['#f56954', '#00a65a', '#f39c12', '#eee6e0', '#8f7b5f']
            }]
        },
        options: {
            legend: {
                display: true
            },
            maintainAspectRatio: false,
            responsive: true
        }
    });
});
