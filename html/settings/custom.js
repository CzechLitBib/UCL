
// FETCH  - JSON { type, value } response JSON { status, value }

async function update(payload) {
	return await fetch('/settings/', {
		method: 'POST',
		headers: { 'Content-Type' :'application/json' },
		body: JSON.stringify(payload)
	})
	.then(response => {
		if (!response.json()) {
			throw new Error('Network error.');
	}
		 return response;
	})
	.catch(error => {
		console.error(error);
		return error;
	});
}

// ERROR

const error_code = document.getElementById('error-code');
error_code.addEventListener('change', error_code_change);

async function error_code_change() {

	code = document.getElementById('error-code').value;

	if (error_code) {
		payload = { 'type':'error', 'data':code };
		const ret = await update(payload);
	
		// update ..
		//document.getElementById('error-label').value = ret['label'];
		//document.getElementById('error-text').value = ret['text'];
	}
}

function error_on_save() {
	document.getElementById('error-save').click();
}

function error_on_delete() {
	document.getElementById('error-delete').click();
}

