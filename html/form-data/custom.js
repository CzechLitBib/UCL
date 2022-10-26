
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
function on_done() {
	modal = new bootstrap.Modal(document.getElementById('modal'));
        document.getElementById('modal-text').textContent = 'Chcete označit záznam jako zpracovaný';
        modal.toggle();
}

