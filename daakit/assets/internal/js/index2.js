"use strict"

/*--details-chart2--*/
var options = {
    chart: {
        height: 280,
        type: 'bar',
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '80%',
            borderRadius: 4,	
        },
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    legend: {
        show: true,
        position: 'top',
        horizontalAlign: 'center',
        fontWeight: 500,
        fontSize: '11px',
        tooltipHoverFormatter: function (val, opts) {
            return val + ' - ' + opts.w.globals.series[opts.seriesIndex][opts.dataPointIndex] + ''
        },
        labels: {
            colors: '#74767c',
        },
        markers: {
            width: 10,
            height: 10,
            strokeWidth: 0,
            radius: 12,
            offsetX: 0,
            offsetY: 1
        },
    },
    series: [{
        name: 'Machines1',
        data: [74, 85, 57, 56, 81, 58, 63, 70, 66, 89, 95, 67]
    }, {
        name: 'Machines2',
        data: [46, 55, 51, 98, 67, 65, 91, 84, 94, 85, 89, 72]
    }, 
     {
        name: 'Machines3',
        data: [50, 65, 49, 78, 50, 95, 80, 55, 39, 48, 60, 45]
    },
     {
        name: 'Machines4',
        data: [38, 45, 17, 60, 30, 52, 98, 55, 88, 60, 50, 62]
    },{
        name: 'Machines5',
        data: [35, 41, 36, 26, 45, 48, 52, 53, 41, 67, 59, 37]
    }],
    xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        axisBorder: {
            show: true,
            color: 'rgba(119, 119, 142, 0.05)',
            offsetX: 0,
            offsetY: 0,
          },
          axisTicks: {
            show: true,
            borderType: 'solid',
            color: 'rgba(119, 119, 142, 0.05)',
          }
    },
    fill: {
        opacity: 1

    },
    grid: {
        show: true,
        borderColor: "rgba(119, 119, 142, 0.1)",
    },
    colors: ['#564ec1', '#04cad0', '#f5334f', '#f7b731', '#21c44c'],
    tooltip: {
        y: {
            formatter: function (val) {
                return  val 
            }
        }
    }
}
var chart = new ApexCharts(document.querySelector("#machines"),options);
chart.render();
/*--machines--*/

// linegraph1
var options = {
    chart: {
        type: 'line',
        height: 30,
        width: 100,
        sparkline: {
        enabled: true
        },
        dropShadow: {
        enabled: false,
        blur: 3,
        opacity: 0.2,
        }
        },
        stroke: {
        show: true,
        curve: 'smooth',
        lineCap: 'butt',
        colors: undefined,
        width: 2,
        dashArray: 0,
        },
        fill: {
        gradient: {
        enabled: false
        }
    },
    series: [{
        name: 'Total Income',
        data: [0, 30, 10, 35, 26, 31, 14, 22, 40, 12]
    }],
    yaxis: {
        min: 0
    },
    colors: ['#305cfc'],
};
var chart = new ApexCharts(document.querySelector("#line-graph1"), options);
chart.render();

//linegarph2
var options = {
    chart: {
        type: 'line',
        height: 30,
        width: 100,
        sparkline: {
        enabled: true
        },
        dropShadow: {
        enabled: false,
        blur: 3,
        opacity: 0.2,
        }
        },
        stroke: {
        show: true,
        curve: 'smooth',
        lineCap: 'butt',
        colors: undefined,
        width: 2,
        dashArray: 0,
        },
        fill: {
        gradient: {
        enabled: false
        }
    },
    series: [{
        name: 'Total Income',
        data: [0, 20, 15, 25, 15, 25, 6, 25, 32, 15]
    }],
    yaxis: {
        min: 0
    },
    colors: ['#f95b3f'],
};
var chart = new ApexCharts(document.querySelector("#line-graph2"), options);
chart.render();

//linegraph3
var options = {
    chart: {
        type: 'line',
        height: 30,
        width: 100,
        sparkline: {
        enabled: true
        },
        dropShadow: {
        enabled: false,
        blur: 3,
        opacity: 0.2,
        }
        },
        stroke: {
        show: true,
        curve: 'smooth',
        lineCap: 'butt',
        colors: undefined,
        width: 2,
        dashArray: 0,
        },
        fill: {
        gradient: {
        enabled: false
        }
    },
    series: [{
        name: 'Total Income',
        data: [0, 10, 30, 12, 16, 25, 4, 35, 26, 15]
    }],
    yaxis: {
        min: 0
    },
    colors: ['#1fae60'],
};
var chart = new ApexCharts(document.querySelector("#line-graph3"), options);
chart.render();

//linegraph4
var options = {
    chart: {
        type: 'line',
        height: 30,
        width: 100,
        sparkline: {
        enabled: true
        },
        dropShadow: {
        enabled: false,
        blur: 3,
        opacity: 0.2,
        }
        },
        stroke: {
        show: true,
        curve: 'smooth',
        lineCap: 'butt',
        colors: undefined,
        width: 2,
        dashArray: 0,
        },
        fill: {
        gradient: {
        enabled: false
        }
    },
    series: [{
        name: 'Total Income',
        data: [0, 12, 19, 26, 10, 18, 8, 17, 35, 14]
    }],
    yaxis: {
        min: 0
    },
    colors: ['#30c2fc'],
};
var chart = new ApexCharts(document.querySelector("#line-graph4"), options);
chart.render();