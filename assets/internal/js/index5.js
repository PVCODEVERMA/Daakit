
"use strict";
	
/*-----echart1-----*/
var chartdata = [{
	name: 'Within Time Limit',
	type: 'bar',
	data: [10, 15, 9, 18, 10, 15]
},{
	name: 'Outof Time Limit',
	type: 'bar',
	data: [10, 14, 10, 15, 9, 25]
}];
var chart = document.getElementById('echart1');
var barChart = echarts.init(chart);
var option = {
	grid: {
		top: '6',
		right: '0',
		bottom: '17',
		left: '25',
	},
	xAxis: {
		data: ['2014', '2015', '2016', '2017', '2018'],
		axisLine: {
			lineStyle: {
				color: 'rgba(119, 119, 142, 0.08)'
			}
		},
		axisLabel: {
			fontSize: 10,
			color: '#9493a9'
		}
	},
	tooltip: {
		show: true,
		showContent: true,
		alwaysShowContent: true,
		triggerOn: 'mousemove',
		trigger: 'axis',
		backgroundColor: '#28273a',
		borderColor: '#28273a',
		trigger: 'axis',
		textStyle: {
			color: '#95a8ba'
		},
		axisPointer: {
			label: {
				show: false,
			}
		}
	},
	yAxis: {
		splitLine: {
			lineStyle: {
				color: 'rgba(119, 119, 142, 0.08)'
			}
		},
		axisLine: {
			lineStyle: {
				color: 'rgba(119, 119, 142, 0.08)'
			}
		},
		axisLabel: {
			fontSize: 10,
			color: '#9493a9'
		}
	},
	series: chartdata,
	color: ['#564ec1', '#ff9900']
};
barChart.setOption(option);
window.addEventListener('resize',function(){
	barChart.resize();
})


/* Chartjs (#inventory) */
var myCanvas = document.getElementById("inventory");
myCanvas.height="272";
var myCanvasContext = myCanvas.getContext("2d");
var gradientStroke1 = myCanvasContext.createLinearGradient(0, 0, 0, 380);
gradientStroke1.addColorStop(0, '#564ec1');
gradientStroke1.addColorStop(1, '#564ec1');

var myChart = new Chart(myCanvas, {
	type: 'bar',
	data: {
		labels: ["Risk", "Service", "Storage", "Admin", "Freight"],
		datasets: [{
			label: 'Carrying Costs Of Inventory',
			data: [16, 8, 4, 8, 16],
			backgroundColor: gradientStroke1,
			hoverBackgroundColor: gradientStroke1,
			hoverBorderWidth: 2,
			hoverBorderColor: 'gradientStroke1'
		}
		]
	},
	options: {
		responsive: true,
		barPercentage: 0.3,
		maintainAspectRatio: true,
		plugins: {
			legend: {
				display: false,
				labels: {
					display: false
				}
			},
			tooltip: {
				enabled: true
			}			
		},
		scales: {
			x: {
					barPercentage: 0.3,
				ticks: {
					color: "#9ba6b5",

				},
				display: true,
				grid: {
					display: true,
					color: 'rgba(119, 119, 142, 0.08)',
					drawBorder: false,
				},
				scaleLabel: {
					display: false,
					labelString: 'Month',
					fontColor: '#000'
				}
			},
			y: {
				ticks: {
					color: "#9ba6b5",
					},
				display: true,
				grid: {
					display: true,
					color: 'rgba(119, 119, 142, 0.08)',
					drawBorder: false,
				},
				scaleLabel: {
					display: false,
					labelString: 'sales',
					fontColor: 'transparent'
				}
			}
		},
		title: {
			display: false,
			text: 'Normal Legend'
		}
	}
});
/* Chartjs (#inventory) closed */
