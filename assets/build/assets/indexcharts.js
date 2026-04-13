import ApexCharts from 'apexcharts'

export function conversionChart(myVarVal) {
	var options = {
		series: [{
			name: 'Paying Conversion rate',
			type: 'line',
			data: [-15, 32, -11, 63, 16, 82, 292, 107, -18, 56, 200, 80],
		}, {
			name: 'Signup Conversion rate',
			type: 'column',
			data: [104, 102, 117, 146, 118, 115, 220, 103, 83, 114, 265, 174],
		}, {
			name: 'Churn rate',
			type: 'column',
			data: [-34, -42, -97, -56, -71, -175, -60, -34, -56, -78, -119, -53]
		}],
		chart: {
			height: 300,
			toolbar: {
				show: false
			}
		},
		stroke: {
			curve: 'smooth',
			lineCap: 'butt',
			colors: undefined,
			dashArray: [0, 0, 0],
			width: [2, 0, 0]
		},
		fill: {
			opacity: [1, 1, 1]
		},
		grid: {
			borderColor: 'rgba(119, 119, 142, 0.08)',
		},
		colors: ["#f7b731", "rgb(" + myVarVal + ")", "#04cad0"],
		plotOptions: {
			bar: {
				colors: {
					ranges: [{
						from: -100,
						to: -46,
						color: '#04cad0'
					}, {
						from: -45,
						to: 0,
						color: '#04cad0'
					}]
				},
				columnWidth: '40%',
				borderRadius: [2, 2]
			}
		},
		dataLabels: {
			enabled: false,
		},
		legend: {
			show: true,
			position: 'top',
			horizontalAlign: 'right',
			fontSize: '12px',
			fontWeight: 500,
			labels: {
				colors: '#74767c',
			},
			markers: {
				width: 8,
				height: 8,
				strokeWidth: 0,
				radius: 12,
				offsetX: 0,
				offsetY: 0
			},
		},
		yaxis: {
			title: {
				style: {
					color: '	#adb5be',
					fontSize: '14px',
					fontFamily: 'poppins, sans-serif',
					fontWeight: 600,
					cssClass: 'apexcharts-yaxis-label',
				},
			},
			labels: {
				formatter: function (y) {
					return y.toFixed(0) + "";
				}
			}
		},
		xaxis: {
			type: 'month',
			categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'sep', 'oct', 'nov', 'dec'],
			axisBorder: {
				show: false,
				color: 'rgba(119, 119, 142, 0.05)',
				offsetX: 0,
				offsetY: 0,
			},
			axisTicks: {
				show: true,
				borderType: 'solid',
				color: 'rgba(119, 119, 142, 0.05)',
				width: 6,
				offsetX: 0,
				offsetY: 0
			},
			labels: {
				rotate: -90
			}
		}

	};
	document.getElementById('conversion').innerHTML = ''; 
	var chart1 = new ApexCharts(document.querySelector("#conversion"), options);
	chart1.render();
}

/* Return chart */
export function returnItens(myVarVal) {
    var options = {
        series: [{
                    name: 'Broken',
                    data: [51, 44, 55, 42, 58,50, 62, 44, 55, 42, 58, 51],
                    },{
                    name: 'NO Reasons',
                    data: [56, 58, 38, 50, 64,45, 55, 58, 38, 50, 64, 56]
                }],
        chart: {
            height: 340,
            type: 'line',
            toolbar: {
                show: false,
            },
            zoom: {
                enabled: false
            },
            dropShadow: {
                enabled: true,
                enabledOnSeries: undefined,
                top: 5,
                left: 0,
                blur: 3,
                color: '#000',
                opacity: 0.15
            },
        },
        dataLabels: {
            enabled: false
        },
        grid: {
			borderColor: 'rgba(119, 119, 142, 0.08)',
		},
        stroke: {
            width: [2, 2],
            curve: 'smooth'
        },
        colors: ["rgb(" + myVarVal + ")", "#04cad0"],
        title: {
            // text: 'Performance Statistics',
            align: 'left',
            style: {
                fontSize: '13px',
                fontWeight: 'bold',
                color: '#8c9097'
            },
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'right',
            fontWeight: 600,
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
        markers: {
            discrete: [{
                seriesIndex: 0,
                dataPointIndex: 4,
                fillColor: '#fff',
                strokeColor: "rgb(" + myVarVal + ")",
                size: 3,
                shape: "circle"
                }, {
                seriesIndex: 1,
                dataPointIndex: 6,
                fillColor: '#fff',
                strokeColor: '#27a5fe',
                size: 3,
                shape: "circle"
            }],
            hover: {
                sizeOffset: 6
            }
        },
        xaxis: {
            categories: ['01 Jan', '02 Jan', '03 Jan', '04 Jan', '05 Jan', '06 Jan', '07 Jan', '08 Jan', '09 Jan',
                '10 Jan', '11 Jan', '12 Jan'
            ],
            axisBorder: {
				show: true,
				color: 'rgba(119, 119, 142, 0.05)',
			},
			axisTicks: {
				show: true,
				color: 'rgba(119, 119, 142, 0.05)',
			},
            labels: {
                show: true,
                rotate: -90,
                style: {
                    colors: "#8c9097",
                    fontSize: '11px',
                    fontWeight: 600,
                    cssClass: 'apexcharts-xaxis-label',
                },
            }
        },
        yaxis: {
            labels: {
                show: true,
                style: {
                    colors: "#8c9097",
                    fontSize: '11px',
                    fontWeight: 600,
                    cssClass: 'apexcharts-xaxis-label',
                },
            }
        },
        tooltip: {
            y: [
                {
                    title: {
                        formatter: function (val) {
                            return val + " (mins)"
                        }
                    }
                },
                {
                    title: {
                        formatter: function (val) {
                            return val + " per session"
                        }
                    }
                },
                {
                    title: {
                        formatter: function (val) {
                            return val;
                        }
                    }
                }
            ]
        }
    };
    document.querySelector("#retunchart").innerHTML = "";
	var chart = new ApexCharts(document.querySelector("#retunchart"), options);
	chart.render();
}

// customer visitors
export function totalVisitors(myVarVal) {
	var options = {
		chart: {
			height: 320,
			type: 'area',
			stacked: true,
			events: {
			  selection: function(chart, e) {
				console.log(new Date(e.xaxis.min) )
			  }
			},
			toolbar: {
				show: false
			}
		},
		colors: ["rgb(" + myVarVal + ")", '#04cad0'],
		dataLabels: {
		  enabled: false
		},
		stroke: {
			curve: 'smooth',
			width: [2, 2]
		},
		grid: {
			borderColor: 'rgba(119, 119, 142, 0.08)',
		},
		legend: {
			show: true,
			position: 'top',
			horizontalAlign: 'right',
			fontSize: '11px',
			fontWeight: 600, 
			labels: {
				colors: '#74767c',
			},
			markers: {
				width: 8,
				height: 8,
				strokeWidth: 0,
				radius: 12,
				offsetX: 0,
				offsetY: 0
			},
		},
		series: [{
				name: 'Total Customers',
				data: [34, 24, 44, 36, 56, 48, 67, 46, 78, 56, 45, 68]
			},{
				name: 'Total Visitors',
				data: [40, 31, 52, 43, 64, 55, 76, 57, 88, 69, 42, 75]
		}],
		fill: {
			gradient: {
			  enabled: true,
			  opacityFrom: 0.6,
			  opacityTo: 0.8,
			}
		},
		xaxis: {
			labels: {
				formatter: function(val) {
					return val + ""
				}
			},
			categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			axisBorder: {
				show: true,
				color: 'rgba(119, 119, 142, 0.05)',
			  },
			  axisTicks: {
				show: true,
				color: 'rgba(119, 119, 142, 0.05)',
			  },
		},
	  yaxis: {
		title: {
		  text: undefined
		},
	  },
	}
	document.querySelector("#totalvisitors").innerHTML = "";
	var chart = new ApexCharts(document.querySelector("#totalvisitors"), options);
	chart.render();
	
}

/*-----total-orders-----*/
export function totalOders(myVarVal) {
	var options = {
		series: [{
			name: 'Orders',
			type: 'column',
			data: [1.8, 2.0, 2.5, 3.2, 2.5, 4.5, 1.5, 2.5, 2.8, 3.8, 3.2, 4.6]
		}, {
			name: 'Sales',
			type: 'column',
			data: [1.1, 1.5, 2.2, 5.2, 3.1, 3.1, 4, 4.1, 4.9, 6.5, 4.5, 7.5]
		}
		],
		chart: {
			height: 300,
			type: 'bar',
			stacked: false,
			dropShadow: {
				enabled: true,
				enabledOnSeries: undefined,
				top: 5,
				left: 0,
				blur: 3,
				color: 'var(--primary-05)',
				opacity: 0.5
			},
		},
		grid: {
			borderColor: 'rgba(119, 119, 142, 0.08)',
		},
		dataLabels: {
			enabled: false
		},
		title: {
			text: undefined,
			align: 'left',
			offsetX: 110
		},
		xaxis: {
			categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			axisBorder: {
				color: 'rgba(119, 119, 142, 0.05)',
				offsetX: 0,
				offsetY: 0,
			},
			axisTicks: {
				color: 'rgba(119, 119, 142, 0.05)',
				width: 6,
				offsetX: 0,
				offsetY: 0
			},
		},
		yaxis: {
			show: true,
			axisTicks: {
				show: true,
			},
			axisBorder: {
				show: false,
				color: '#4eb6d0'
			},
			labels: {
				style: {
					colors: '#4eb6d0',
				}
			},
			title: {
				text: undefined,
				style: {
					color: '#4eb6d0',
				}
			},
			tooltip: {
				enabled: true
			}
		},
		tooltip: {
			enabled: true,
		},
		colors: ["rgb(" + myVarVal + ")", '#04cad0'],
		legend: {
			position: 'bottom',
			offsetX: 40,
			fontSize: '10px',
			fontWeight: 600, 
			labels: {
				colors: '#74767c',
			},
			markers: {
				width: 9,
				height: 9,
				strokeWidth: 0,
				radius: 12,
				offsetX: 0,
				offsetY: 0
			},
		}, 
		stroke: {
			width: [0, 0, 1.5],
			curve: 'smooth',
			dashArray: [0, 0, 2],
		},
		plotOptions: {
			bar: {
				columnWidth: "35%",
				borderRadius: 3
			}
		},
	};
	document.querySelector("#total-orders").innerHTML = "";
	var chart = new ApexCharts(document.querySelector("#total-orders"), options);
	chart.render();
}

/* Apex chart */
var options = {
    chart: {
        height: 290,
        type: 'line',
        toolbar: {
            show: false
        }
    },
    series: [{
        name: 'Users',
        type: 'column',
        data: [148, 268, 157, 248, 179, 284, 185, 289, 158, 102, 325, 78]
    }, {
        name: 'Sessions',
        type: 'line',
        data: [245, 340, 270, 380, 310, 260, 360, 240, 320, 240,220, 280]
    }, {
        name: 'Pageviews',
        type: 'line',
        data: [190, 240, 340, 260, 390, 280, 340, 270, 380, 340, 350, 420]
    }],
    stroke: {
        width: [0, 3, 2],
        curve: ['', 'smooth', 'straight']
    },
    grid: {
        show: true,
        borderColor: "rgba(119, 119, 142, 0.1)",
    },
    legend: {
        show: true,
        position: 'top',
          horizontalAlign: 'right',
        fontSize: '11px',
        fontWeight: 600, 
        labels: {
            colors: '#74767c',
        },
        markers: {
            width: 8,
            height: 8,
            strokeWidth: 0,
            radius: 12,
            offsetX: 0,
            offsetY: 0
        },
    },
    plotOptions: {
        bar: {
            columnWidth: "26%",
            borderRadius: 2,
        },
    },
    title: {
        text: undefined
    },
    xaxis: {
    categories: ['01 Jan 2001', '02 Jan 2001', '03 Jan 2001', '04 Jan 2001', '05 Jan 2001', '06 Jan 2001', '07 Jan 2001', '08 Jan 2001', '09 Jan 2001', '10 Jan 2001', '11 Jan 2001', '12 Jan 2001'],
    type: 'datetime',
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
        width: 6,
        offsetX: 0,
        offsetY: 0
        },
    },
    yaxis: [{
        title: {
          text: 'Users/Sessions',
        },
    }],
    colors: ['#564ec1', '#04cad0', '#f7b731'],
}
var chart2 = document.querySelector("#analytic") && new ApexCharts(document.querySelector("#analytic"), options);
if (chart2){
	chart2.render();
}

export function analyticsChart(myVarVal) {
	console.log(chart2);
    chart2.updateOptions({ colors: ["rgb(" + myVarVal + ")", "#f7b731", "#04cad0"] });
}
/* Apex chart closed */

/* Apex chart */
var options = {
	series: [{
	  name: "Week",
	  data: [23, 11, 22, 35, 17, 28, 22, 37, 21, 44, 22, 30]
	},
	{
	  name: 'Month',
	  data: [30, 25, 46, 28, 21, 45, 35, 64, 52, 59, 36, 39]
	}
  ],
	chart: {
	height: 290,
	type: 'area',
	zoom: {
	  enabled: false
	},
	toolbar: {
		show: false,
	}
  },
  dataLabels: {
	enabled: false
  },
  stroke: {
	width: [2, 2],
	curve: 'smooth',
	dashArray: [0, 8]
  },
  legend: {
	tooltipHoverFormatter: function(val, opts) {
	  return val + ' - ' + opts.w.globals.series[opts.seriesIndex][opts.dataPointIndex] + ''
	}
  },
  markers: {
	size: 0,
	hover: {
	  sizeOffset: 6
	}
  },
  legend: {
	show: true,
		position: 'top',
		horizontalAlign: 'right',
		fontSize: '12px',
		fontWeight: 600, 
		labels: {
			colors: '#74767c',
		},
		markers: {
			width: 10,
			height: 10,
			strokeWidth: 0,
			radius: 12,
			offsetX: 0,
			offsetY: 0
		},
	},
  colors: ['#564ec1', '#5eba00'],
  xaxis: {
	categories: ['01 Jan', '02 Feb', '03 Mar', '04 Apr', '05 May', '06 Jun', '07 Jul', '08 Aug', '09 Sep',
	  '10 Oct', '11 Nov', '12 Dec'
	],
	axisBorder: {
		show: true,
		color: 'rgba(119, 119, 142, 0.05)',
	},
	axisTicks: {
		show: true,
		color: 'rgba(119, 119, 142, 0.05)',
	}
  },
  grid: {
	borderColor: 'rgba(119, 119, 142, 0.1)'
  }
};

var chart5 = document.querySelector("#perfectorder") && new ApexCharts(document.querySelector("#perfectorder"), options);

if (chart5){
	chart5.render();
}

export function orderRate(myVarVal) {
	chart5.updateOptions({ colors: ["rgb(" + myVarVal + ")", "#04cad0"] });
}

