
// FETCH  - JSON { type, value } response JSON { value }
async function update(payload) {
	return await fetch('/form-data/', {
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

// FETCH - PDF
async function export_data(payload) {
	return await fetch('/form-data/', {
		method: 'POST',
		headers: {'Content-Type': 'application/json'},
		body: JSON.stringify(payload)
	})
	.then(response => {
		if (!response.ok) {
			throw new Error('Network error.');
	}
		 return response.blob();
	})
        .then(blob => {
		var url = window.URL.createObjectURL(blob);
		var a = document.createElement('a');
		a.href = url;
		a.download = payload['data'] + '.pdf';
	
		document.body.appendChild(a);
		a.click();
		a.remove();
		return true;
        })
	.catch(error => {
		console.error(error);
		return false;
	});
}

// collapse
function toggle_data(id) {
	myCollapse = document.getElementById('collapse-' + id);
	bsCollapse = new bootstrap.Collapse(myCollapse, {
	toggle: false
	})

	bsCollapse.toggle();
}

// confirmation
async function on_confirm(id) {
	payload = {'type':'update', 'data':id};
	const ret = await update(payload);
	if (ret.length !== 0) {
		if (ret['value'] === 'on') {
			document.getElementById('btn-' + id).style.background='#dc3545';
			document.getElementById('btn-' + id).style.borderColor='#dc3545';
		}
		if (ret['value'] === 'off') {
			document.getElementById('btn-' + id).style.background='#6c757d';
			document.getElementById('btn-' + id).style.borderColor='#6c757d';
		}
	}
}

// PDF
async function get_pdf(id) {
	payload = {'type':'file', 'data':id};
	const ret = await this.export_data(payload);
}

