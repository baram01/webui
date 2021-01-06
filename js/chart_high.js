/*
    Copyright (C) 2017  3 Young, Inc



    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.



    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.



    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

var stat_chart = new Highcharts.chart('conf_stat', {
	exporting: {
		enabled: false
	},

	title: {
		text: 'Conferences per hour'
	},

	subtitle: {
		text: 'source: last-6'
	},

	yAxis: {
		title: {
			text: '# of Conferences'
		}
	},

	legend: {
		enabled: false
	},

	series: [{
		data: [ ]
	}]

});

var par_chart = new Highcharts.chart('conf_par', {
	exporting: {
		enabled: false
	},

	title: {
		text: 'Sessions per hour'
	},

	subtitle: {
		text: 'source: last-6'
	},

	yAxis: {
		title: {
			text: '# of Sessions'
		}
	},

	legend: {
		enabled: false
	},

	series: [{
		data: [ ]
	}]
});

var percent_chart = new Highcharts.chart('conf_percent', {
	chart: {
		plotBackgroundColor: null,
		plotBorderWidth: null,
		plotShadow: false,
		type: 'pie'
	},

	exporting: {
		enabled: false
	},

	title: {
		text: 'Conferences percentage by sessions'
	},

	subtitle: {
		text: 'source: last-hour'
	},

	tooltip: {
		pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
	},

	plotOptions: {
		allowPointSelect: true,
		cursor: 'pointer',
		dataLabels: {
			enabled: true,
			format: '<br>{point.name}</b>: {point.percentage:.1f}%',
			style: {
				color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
			}
		}
	},
/*
	data: {
		csv: document.getElementById('csv').innerHTML
	},
*/
        series: [{
                name: 'Percent',
                colorByPoint: true,
                data: [{
                        name: 'sessions',
                        y: 548
                }]
        }]

});

