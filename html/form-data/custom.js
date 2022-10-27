
// MODAL
modal = new bootstrap.Modal(document.getElementById('modal'));
modal_action = null;

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

// drop form-data
async function drop_prescription(id) {
	return await fetch('/form-data/', {
		method: 'POST',
		body: 'drop:' + id
	})
	.then(response => {
		if (!response.ok) {
			throw new Error('Network error.');
	}
		 return response.text();
	})
	.catch(error => {
		console.error(error);
		return error;
	});
}

async function remove_form_data(id) {
	const ret = await this.drop_prescription(id);
	if (ret === 'ok') {
		document.getElementById(id).style.display = 'none';	
	}
}

// collapse
function toggle_data(id) {
	myCollapse = document.getElementById('collapse-' + id);
	bsCollapse = new bootstrap.Collapse(myCollapse, {
	toggle: false
	})

	bsCollapse.toggle();
}

// modal
function confirmation(id) {
        document.getElementById('modal-text').textContent = 'Chcete označit záznam jako zpracovaný';
	modal_action = id;
        modal.toggle();
}

// confirmation
async function on_confirm() {
	payload = {'type':'visible', 'data':modal_action};
	const ret = await update(payload);
	if (ret.length !== 0) {
		document.getElementById(modal_action).style.display = "none";
		document.getElementById('collapse-' + modal_action).style.display = "none";
	}
	modal_action = null;
	modal.toggle();
}

