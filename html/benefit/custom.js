
var modal = new bootstrap.Modal(document.getElementById('modal'))
var data = new bootstrap.Modal(document.getElementById('data'))

async function auth() {

	let data = new FormData();
	data.append('name',document.getElementById('user').value);
	data.append('pass', document.getElementById('secret').value);

	return await fetch('/sodexo/', {
		method: 'POST',
		body: data 
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

async function login() {
	const ret = await this.auth();
	modal.toggle();
	document.getElementById('list').innerHTML = ret;
	data.toggle();
}

function mod() {
	modal.toggle();
}

