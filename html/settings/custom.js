// ERROR

const error_code = document.getelementById('error-code');
error_code.addEvenListener('change', error_code_change);

function error_code_change() {

	code = document.getelementById('error-code').value;

	if (error_code) {
		payload = { 'type':'error', 'data':code }

		// fetch ..
		ret = await this.fetch(payload);
	
		// update ..
		document.getElementById('error-label').value = ret['label'];
		document.getElementById('error-text').value = ret['text'];
	}
}

function error_on_save() {
	document.getElementById('error-save').submit();
}

function error_on_delete() {
	document.getElementById('error-delete').submit();
}

// FETCH  - JSON { type, value } response JSON { status, value }
async function fetch(payload) {
	return await fetch('/settings/', {
		method: 'POST',
		headers: { 'Content-Type' :'application/json' },
		body: JSON.stringify(payload)
	})
	.then(response.json() => {
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

