
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

