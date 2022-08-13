
// MODAL
modal = new bootstrap.Modal(document.getElementById('modal'));

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
		return [];
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
	document.getElementById('error-save').click();
}

function error_on_delete() {
	document.getElementById('error-delete').click();
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
	modal.toggle();
	//document.getElementById('user-save').click();
}

function user_on_delete() {
	document.getElementById('user-delete').click();
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
	document.getElementById('review-save').click();
}

function review_on_delete() {
	document.getElementById('review-delete').click();
}

// CODE

function code_on_save() {
	document.getElementById('code-save').click();
}

