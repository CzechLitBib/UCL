
// MODAL

modal = new bootstrap.Modal(document.getElementById('modal'));
modal_action = null;

function on_confirm() {
	if (modal_action == 'access-save') { document.getElementById('access-save').click(); }
	if (modal_action == 'group-save') { document.getElementById('group-save').click(); }
	if (modal_action == 'group-delete') { document.getElementById('group-delete').click(); }
	if (modal_action == 'module-save') { document.getElementById('module-save').click(); }
	if (modal_action == 'module-delete') { document.getElementById('module-delete').click(); }
	modal_action = null;
	modal.toggle();
}

// FETCH  - JSON { type, value } response JSON { value }
async function update(payload) {
	return await fetch('/access/', {
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

// GROUP

function group_on_save() {
	document.getElementById('modal-text').textContent = 'Chcete vytvořit skupinu ';
	document.getElementById('modal-text-bold').textContent = document.getElementById('group-new').value;
	modal.toggle();
	modal_action = 'group-save';
}

function group_on_delete() {
	document.getElementById('modal-text').textContent = 'Chcete odstranit skupinu ';
	document.getElementById('modal-text-bold').textContent = document.getElementById('group-new').value;
	modal.toggle();
	modal_action = 'group-delete';
}

// MODULE

function module_on_save() {
	document.getElementById('modal-text').textContent = 'Chcete vytvořit modul ';
	document.getElementById('modal-text-bold').textContent = document.getElementById('module-new').value;
	modal.toggle();
	modal_action = 'module-save';
}

function module_on_delete() {
	document.getElementById('modal-text').textContent = 'Chcete odstranit modul ';
	document.getElementById('modal-text-bold').textContent = document.getElementById('module-new').value;
	modal.toggle();
	modal_action = 'module-delete';
}

// ACCESS

async function group_on_change() {
	// get group
	groups = document.getElementsByName('group-option');
	for(var i=0; i<groups.length; i++) {
		if (groups[i].checked) {
			selector = 'label[for=' + groups[i].id + ']';
			label = document.querySelector(selector);
			text = label.innerHTML;
		}
	}
	// update user
	payload = {'type':'user', 'data':group};
	ret = await update(payload);
	//if (ret.length !== 0) {
	//	document.getElementById('code-data').value = ret['value'].join('\n');
	//}
	// update module
	payload = {'type':'module', 'data':group};
	ret = await update(payload);
	//if (ret.length !== 0) {
	//	document.getElementById('code-data').value = ret['value'].join('\n');
	//}
}

