
// chart
var ctx = document.getElementById("donut");
var myChart = new Chart(ctx, {
	type: 'doughnut',
	data: {
	datasets: [{
		label: '# of Tomatoes',
		data: [243, 123, 68, 45, 13, 10],
		backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)'],
		borderColor: ['rgba(255,99,132,1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)'],
		borderWidth: 1
	}]
	},
	plugins: ['chartjs-plugin-labels'],
	options: {
		responsive: false,
		plugins: {
			labels: {
				render: 'value'
			}
		}
  	}
});

var ctx = document.getElementById("donut2");
var myChart = new Chart(ctx, {
	type: 'doughnut',
	data: {
	datasets: [{
		label: '# of Tomatoes',
		data: [243, 123, 68, 45, 13, 10],
		backgroundColor: ['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)'],
		borderColor: ['rgba(255,99,132,1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)'],
		borderWidth: 1
	}]
	},
	plugins: ['chartjs-plugin-labels'],
	options: {
		responsive: false,
		plugins: {
			labels: {
				render: 'value'
			}
		}
  	}
});

