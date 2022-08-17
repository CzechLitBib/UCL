
// MODAL

modal_list = new bootstrap.Modal(document.getElementById('modal-list'));
modal = new bootstrap.Modal(document.getElementById('modal'));
modal_action = null;

function on_confirm() {
	if (modal_action == 'error-save') { document.getElementById('error-save').click(); }
	if (modal_action == 'error-delete') { document.getElementById('error-delete').click(); }
	if (modal_action == 'user-save') { document.getElementById('user-save').click(); }
	if (modal_action == 'user-delete') { document.getElementById('user-delete').click(); }
	if (modal_action == 'review-save') { document.getElementById('review-save').click(); }
	if (modal_action == 'review-delete') { document.getElementById('review-delete').click(); }
	if (modal_action == 'code-save') { document.getElementById('code-save').click(); }
	modal_action = null;
	modal.toggle();
}

// FETCH - LISTS

async function modal_data(payload) {
	return await fetch('/settings/', {
		method: 'POST',
		headers: { 'Content-Type' :'application/json' },
		body: JSON.stringify(payload)
	})
	.then(response => {
		if (!response.ok) {
			throw new Error('Network error.');
		}
		return response.text();
	})
	.catch(error => {
		console.error(error);
		return;
	});
}

async function on_display(type) {
	payload = { 'type':type, 'data':'list' };
	const ret = await this.modal_data(payload);
	document.getElementById('modal-list-data').innerHTML = ret;
	modal_list.toggle();
}

// FETCH - EXPORT

async function on_export(type) {
	payload = { 'type':type, 'data':'export' };
	const ret = await this.modal_data(payload);
}

// FETCH  - JSON { type, value } response JSON { value }
async function update(payload) {
	return await fetch('/settings/', {
		method: 'POST',
		headers: { 'Content-Type' :'application/json' },
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
	if (error_code) {
		payload = { 'type':'error', 'data':code };
		const ret = await update(payload);
		if (ret.length !== 0) {
			document.getElementById('error-label').value = ret['value']['label'];
			document.getElementById('error-text').value = ret['value']['text'];
		}
	}
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

// USER

const user_code = document.getElementById('user-code');
user_code.addEventListener('input', user_code_change);

async function user_code_change() {
	code = document.getElementById('user-code').value;
	if (user_code) {
		payload = { 'type':'user', 'data':code };
		const ret = await update(payload);
		if (ret.length !== 0) {
			document.getElementById('aleph').value = ret['value']['aleph'];
			document.getElementById('email').value = ret['value']['email'];
		}
	}
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
	if (review_authority) {
		payload = { 'type':'review', 'data':authority };
		const ret = await update(payload);
		if (ret.length !== 0) {
			document.getElementById('review-name').value = ret['value']['name'];
		}
	}
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

