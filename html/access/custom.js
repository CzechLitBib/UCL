
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

function access_on_save() {
	groups = document.getElementById("group-option");
	if (groups.selectedIndex >= 0) {
 		group = groups.options[groups.selectedIndex].text;
		document.getElementById('modal-text').textContent = 'Chcete uložit skupinu ';
		document.getElementById('modal-text-bold').textContent = group;
		modal.toggle();
		modal_action = 'access-save';
	}
}

async function group_on_change() {
	groups = document.getElementById("group-option");
 	group = groups.options[groups.selectedIndex].text;
	// update user
	payload = {'type':'user', 'data':group};
	ret = await update(payload);
	if (ret.length !== 0) {
		document.getElementById('user-list').value = ret['value'].join('\n');
	}
	// update module
	payload = {'type':'module', 'data':group};
	ret = await update(payload);
	modules = document.getElementsByName('module-list[]');
	for(var i=0; i<modules.length; i++) {
		if (ret['value'].includes(modules[i].id)) {
			document.getElementById(modules[i].id).checked = true;
		} else {
			document.getElementById(modules[i].id).checked = false;
		}	
	}
}

