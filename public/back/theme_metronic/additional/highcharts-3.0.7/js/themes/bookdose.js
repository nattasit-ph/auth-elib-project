/**
 * Grid theme for Highcharts JS
 * @author Torstein Hønsi
 */

Highcharts.theme = {
	// colors: ['#57C8F2', '#A9D96C', '#3ECCC0', '#FF6C60', '#F8D347', '#8075C4'],
	colors: ['#BFC2CD', '#FF6C60', '#F8D347', '#8075C4', '#57C8F2', '#A9D96C'],
	chart: {
		backgroundColor: null,
		borderWidth: 0,
		plotBackgroundColor: null,
		plotBorderWidth: null,
		plotShadow: false,
		marginTop:20
	},
	title: {
		text: ''
	},
	credits: {
		enabled: false
	},
	exporting: {
		enabled: false
	},
	subtitle: {
		style: {
			color: '#666666',
			font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
		}
	},
	xAxis: {
		tickInterval: null,
		tickmarkPlacement: null,
		gridLineWidth: 0,
		lineColor: '#C0C0C0',
		tickWidth: 0,
		tickColor: '#555555',
		labels: {
			style: {
				color: '#555555',
				font: '11px Trebuchet MS, Verdana, sans-serif'
			}
		},
		title: {
			style: {
				color: '#333',
				fontWeight: 'bold',
				fontSize: '12px',
				fontFamily: 'Trebuchet MS, Verdana, sans-serif'

			}
		}
	},
	yAxis: {
		gridLineColor: '#C0C0C0',
		minorTickInterval: null,
		lineColor: null,
		lineWidth: 1,
		tickWidth: 0,
		gridLineDashStyle:"Dash",
		tickColor: '#555555',
		labels: {
			style: {
				color: '#555555',
				font: '11px Trebuchet MS, Verdana, sans-serif'
			}
		},
		title: {
			style: {
				color: '#333',
				fontWeight: 'bold',
				fontSize: '12px',
				fontFamily: 'Trebuchet MS, Verdana, sans-serif'
			}
		}
	},
	legend: {
		itemStyle: {
			font: '9pt Trebuchet MS, Verdana, sans-serif',
			color: 'black'

		},
		itemHoverStyle: {
			color: '#039'
		},
		itemHiddenStyle: {
			color: 'gray'
		}
	},
	labels: {
		style: {
			color: '#99b'
		}
	},
	navigation: {
		buttonOptions: {
			theme: {
				stroke: '#CCCCCC'
			}
		}
	},
	plotOptions: {
		series: {
			pointPadding:0,
			borderWidth:1,
			maxPointWidth: 10
		},
		pie: {
			size: '80%',
			borderWidth:1,
			pointWidth: 30
		}
	}

};

// Apply the theme
var highchartsOptions = Highcharts.setOptions(Highcharts.theme);
