
// FETCH  - JSON { type, value } response JSON { value }
async function update(payload) {
	return await fetch('/cat/', {
		method: 'POST',
		headers: {'Content-Type' :'application/json'},
		body: JSON.stringify(payload)
	})
	.then(response => response.json())
	.then(data => {
		 return data;
	})
	.catch(error => {
		console.error(error);
		return;
	});
}

// cipher on change
async function cipher_on_change() {
	ciphers = document.getElementById("cipher-option");
 	cipher = ciphers.options[ciphers.selectedIndex].text;
	// update cipher detail
	payload = {'type':'cipher', 'data':cipher};
	ret = await update(payload);
	if (ret.length !== 0) {
		document.getElementById('cipher-new').innerHTML = ret['value']['new'];
		document.getElementById('cipher-fix').innerHTML = ret['value']['fix'];
		document.getElementById('cipher-other').innerHTML = ret['value']['other'];
	}
}

// chart

donut_a_labels = ['a','b','c','d','e','f'];
donut_a_data = [243, 123, 68, 45, 13, 10];
donut_a_color = ['#dc3545', '#ea4c46', '#f07470', '#f1959b', '#f6bdc0', '#ffffff'];

var ctx = document.getElementById("donut_a");
var myChart = new Chart(ctx, {
	type: 'doughnut',
	data: {
	labels: donut_a_labels,
	datasets: [{
		data: donut_a_data,
		backgroundColor: donut_a_color,
		borderColor: ['#000000'],
		borderWidth: 1
	}]
	},
	plugins: ['chartjs-plugin-labels'],
	options: {
		responsive: false,
		plugins: {
			tooltip: {
				displayColors: false,
				callbacks: {
					title: function (tooltipItem) { return ''; }
				}
			},
			legend: {
				display: false
			},
			labels: {
				render: 'label'
			}
		}
  	}
});

var ctx = document.getElementById("donut_b");
var myChart = new Chart(ctx, {
	type: 'doughnut',
	data: {
	labels: ['a','b','c','d','e','f'],
	datasets: [{
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
			tooltip: {
				displayColors: false,
				callbacks: {
					title: function (tooltipItem) { return ''; }
				}
			},
			legend: {
				display: false
			},
			labels: {
				render: 'label'
			}
		}
  	}
});

