
// chart
var ctx = document.getElementById("donut");
var myChart = new Chart(ctx, {
	type: 'doughnut',
	data: {
	datasets: [{
		label: 'SIF',
		data: [243, 123, 68, 45, 13, 10],
		backgroundColor: ['#dc3545', '#ea4c46', '#f07470', '#f1959b', '#f6bdc0', '#ffffff'],
		borderColor: ['#000000'],
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
		label: 'SIF',
		data: [834, 222, 133, 74, 5, 1],
		backgroundColor: ['#dc3545', '#ea4c46', '#f07470', '#f1959b', '#f6bdc0', '#ffffff'],
		borderColor: ['#000000'],
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

