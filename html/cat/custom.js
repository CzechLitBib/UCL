
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

// cipher on record
function cipher_on_record() {
	ciphers = document.getElementById("cipher-option");
 	cipher = ciphers.options[ciphers.selectedIndex].text;
	if (cipher.length !== 0) {
		window.location.href = '/cat/data.php?sif=' + encodeURI(cipher);// redirect
	}
}

// chart
doughnut_label = [], doughnut_data = [];
doughnut_color = ['#dc3545', '#ea4c46', '#f07470', '#f1959b', '#f6bdc0', '#f8f9fa'];

async function doughnut_update(model) {
	payload = {'type':'chart', 'data': model};
	ret = await update(payload);
	if (ret.length !== 0) {
		doughnut_label = ret['value']['label'];
		doughnut_data = ret['value']['data'];
	}

	ctx = document.getElementById(model + '-doughnut');
	myChart = new Chart(ctx, {
		type: 'doughnut',
		data: {
		labels: doughnut_label,
		datasets: [{
			data: doughnut_data,
			backgroundColor: doughnut_color,
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
}

if(document.getElementById('A-doughnut')) { doughnut_update('A'); }
if(document.getElementById('B-doughnut')) { doughnut_update('B'); }

// select first
ciphers = document.getElementById("cipher-option");
if (ciphers !== null && ciphers.selectedIndex < 0) {
	ciphers.options[0].selected = true;
	cipher_on_change();
}

