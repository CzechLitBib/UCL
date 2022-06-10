
// JS Fetch API

const spinner = document.getElementById('spinner');
const warn = document.getElementById('alert');

function date() {
	var today = new Date();
	return today.toISOString().substring(0, 10);
}

async function payload(format) {
	return await fetch('/export', {
		method: 'POST',
		headers: {
			'Content-Type': 'text/plain',
		},
		body: format
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
		a.download = 'vufind-' + date() + '.' + format;
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

async function download(format) {
	warn.setAttribute('hidden', '');
	spinner.removeAttribute('hidden');

	const download = await this.payload(format);

	spinner.setAttribute('hidden', '');
	if (!download) {
		warn.removeAttribute('hidden');
	}
}

