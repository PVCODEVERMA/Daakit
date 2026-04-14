$(function(e) {
	'use strict';
	
	
	/*--echart-1---*/
	var myChart2 = echarts.init(document.getElementById('echart-1'));
	var option2 = {
		title: {
			text: '',
			subtext: '',
			x: 'center'
		},
		responsive: true,
		maintainAspectRatio: false,
		tooltip: {
			trigger: 'item',
			formatter: "{a} {b} : {c} ({d}%)"
		},
		legend: {
			x: 'center',
			y: 'bottom',
			data: ['USA', 'India',  'Russia', 'Canada',  'Germany'],
			textStyle: {
				color: '#9493a9'
			}
		},
		toolbox: {
			show: true,
			feature: {
				mark: {
					show: true
				},
				dataView: {
					show: true,
					readOnly: false
				},
				magicType: {
					show: true,
					type: ['pie']
				},
				restore: {
					show: true
				},
				saveAsImage: {
					show: true
				}
			}
		},
		calculable: true,
		series: [{
			name: '',
			type: 'pie',
			radius: [20, 110],
			center: ['50%', '50%'],
			roseType: 'radius',
			label: {
				normal: {
					show: false
				},
				emphasis: {
					show: true
				}
			},
			lableLine: {
				normal: {
					show: false
				},
				emphasis: {
					show: true
				}
			},
			data: [{
				value: 56,
				name: 'USA'
			}, {
				value: 53,
				name: 'India'
			}, {
				value: 46,
				name: 'Russia'
			}, {
				value: 30,
				name: 'Canada'
			},{
				value: 15,
				name: 'Germany'
			}]
		}, ],
		color: ['#564ec1', '#04cad0', '#f5334f', '#f7b731 ', '#26c2f7']
	};
	myChart2.setOption(option2);
	window.addEventListener('resize',function(){
		myChart2.resize();
	})
	/*--echart-1---*/

});
