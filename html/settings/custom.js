
// MODAL

modal = new bootstrap.Modal(document.getElementById('modal'));
modal_action = null;

function on_confirm() {
	if (modal_action == 'error-save') { document.getElementById('error-save').click(); }
	if (modal_action == 'error-delete') { document.getElementById('error-delete').click(); }
	if (modal_action == 'exception-save') { document.getElementById('exception-save').click(); }
	if (modal_action == 'exception-delete') { document.getElementById('exception-delete').click(); }
	if (modal_action == 'user-save') { document.getElementById('user-save').click(); }
	if (modal_action == 'user-delete') { document.getElementById('user-delete').click(); }
	if (modal_action == 'review-save') { document.getElementById('review-save').click(); }
	if (modal_action == 'review-delete') { document.getElementById('review-delete').click(); }
	if (modal_action == 'code-save') { document.getElementById('code-save').click(); }
	if (modal_action == 'dict-save') { document.getElementById('dict-save').click(); }
	modal_action = null;
	modal.toggle();
}

// FETCH - EXPORT

async function export_data(payload,type) {
	return await fetch('/settings/', {
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
	
		if (type == 'dictionary') {
			a.download = payload['dict'] + '.txt';
		} else {
			a.download = type + '.csv';
		}
	
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

async function on_export(type) {
	dict = '';
	payload = {'type':type, 'data':'export'};
	if(type == 'dictionary') {
		dicts = document.getElementsByName('dict-option');
		for(var i=0; i<dicts.length; i++) {
			if (dicts[i].checked) {
				dict = document.getElementById(dicts[i].id).value;
			}
		}
		payload = {'type':type, 'data':'export', 'dict':dict};
	}
	const ret = await this.export_data(payload, type);
}

// FETCH  - JSON { type, value } response JSON { value }
async function update(payload) {
	return await fetch('/settings/', {
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

// ERROR

const error_code = document.getElementById('error-code');
error_code.addEventListener('input', error_code_change);

async function error_code_change() {
	code = document.getElementById('error-code').value;
	label = '';
	text = '';
	if (error_code) {
		payload = {'type':'error', 'data':code};
		const ret = await update(payload);
		if (ret.length !== 0) {
			label = ret['value']['label'];
			text = ret['value']['text'];
		}
	}
	document.getElementById('error-label').value = label;
	document.getElementById('error-text').value = text;
}

function error_on_save() {
	document.getElementById('modal-text').textContent = 'Chcete uložit chybový kód ';
	document.getElementById('modal-text-bold').textContent = document.getElementById('error-code').value;
	modal.toggle();
	modal_action = 'error-save';
}

function error_on_delete() {
	document.getElementById('modal-text').textContent = 'Chcete smazat chybový kód ';
	document.getElementById('modal-text-bold').textContent = document.getElementById('error-code').value;
	modal.toggle();
	modal_action = 'error-delete';
}

// EXCEPTION

const exception_ident = document.getElementById('exception-ident');
exception_ident.addEventListener('input', exception_ident_change);

async function exception_ident_change() {
	ident = document.getElementById('exception-ident').value;
	code = '';
	if (exception_ident) {
		payload = {'type':'exception', 'data':ident};
		const ret = await update(payload);
		if (ret.length !== 0) {
			code = ret['value']['code'];
		}
	}
	document.getElementById('exception-code').value = code;
}

function exception_on_save() {
	document.getElementById('modal-text').textContent = 'Chcete uložit vyjímku ';
	document.getElementById('modal-text-bold').textContent = document.getElementById('exception-ident').value;
	modal.toggle();
	modal_action = 'exception-save';
}

function exception_on_delete() {
	document.getElementById('modal-text').textContent = 'Chcete smazat vyjímku ';
	document.getElementById('modal-text-bold').textContent = document.getElementById('exception-ident').value;
	modal.toggle();
	modal_action = 'exception-delete';
}

// USER

const user_code = document.getElementById('user-code');
user_code.addEventListener('input', user_code_change);

async function user_code_change() {
	code = document.getElementById('user-code').value;
	aleph = '';
	email = '';
	if (user_code) {
		payload = {'type':'user', 'data':code};
		const ret = await update(payload);
		if (ret.length !== 0) {
			aleph = ret['value']['aleph'];
			email = ret['value']['email'];
		}
	}
	document.getElementById('aleph').value = aleph;
	document.getElementById('email').value = email;
}

function user_on_save() {
	document.getElementById('modal-text').textContent = 'Chcete uložit uživatele ';
	document.getElementById('modal-text-bold').textContent = document.getElementById('user-code').value;
	modal.toggle();
	modal_action = 'user-save';
}

function user_on_delete() {
	document.getElementById('modal-text').textContent = 'Chcete smazat uživatele ';
	document.getElementById('modal-text-bold').textContent = document.getElementById('user-code').value;
	modal.toggle();
	modal_action = 'user-delete';
}

// REVIEW

const review_authority = document.getElementById('review-authority');
review_authority.addEventListener('input', review_authority_change);

async function review_authority_change() {
	authority = document.getElementById('review-authority').value;
	name = '';
	if (review_authority) {
		payload = {'type':'review', 'data':authority};
		const ret = await update(payload);
		if (ret.length !== 0) {
			name = ret['value']['name'];
		}
	}
	document.getElementById('review-name').value = name;
}

function review_on_save() {
	document.getElementById('modal-text').textContent = 'Chcete uložit recenzi ';
	document.getElementById('modal-text-bold').textContent = document.getElementById('review-authority').value;
	modal.toggle();
	modal_action = 'review-save';
}

function review_on_delete() {
	document.getElementById('modal-text').textContent = 'Chcete smazat recenzi ';
	document.getElementById('modal-text-bold').textContent = document.getElementById('review-authority').value;
	modal.toggle();
	modal_action = 'review-delete';
}

// CODE

function code_on_save() {
	document.getElementById('modal-text').textContent = 'Chcete uložit kódy';
	document.getElementById('modal-text-bold').textContent = '';
	modal.toggle();
	modal_action = 'code-save';
}

// DICT

async function dict_on_change(dict) {
	payload = {'type':'dict', 'data':dict};
	const ret = await update(payload);
	if (ret.length !== 0) {
		document.getElementById('dict-data').value = ret['value'].join('\n');
	}
}

function dict_on_save() {
	dicts = document.getElementsByName('dict-option');
	for(var i=0; i<dicts.length; i++) {
		if (dicts[i].checked) {
			selector = 'label[for=' + dicts[i].id + ']';
			label = document.querySelector(selector);
			text = label.innerHTML;
		}
	}
	document.getElementById('modal-text').textContent = 'Chcete uložit slovník ';
	document.getElementById('modal-text-bold').textContent = text;
	modal.toggle();
	modal_action = 'dict-save';
}

