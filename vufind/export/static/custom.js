
// JS Fetch API

const spinner = document.getElementById('spinner');
const warn = document.getElementById('alert');
const abort = document.getElementById('cancel');

// abort controller
let controller = new AbortController();

function cancel() {
	controller.abort()
}

function date() {
	var today = new Date();
	return today.toISOString().substring(0, 10);
}

async function payload(format) {
	return await fetch('/export', {
		signal: controller.signal,
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
		switch(format) {
			case 'marcxml':
				a.download = 'vufind-' + date() + '.xml';
				break;
			case 'marc21':
				a.download = 'vufind-' + date() + '.mrc';
				break;
			default:
				a.download = 'vufind-' + date() + '.' + format;
				
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

async function download(format) {
	warn.setAttribute('hidden', '');
	spinner.removeAttribute('hidden');
	abort.removeAttribute('hidden');

	const download = await this.payload(format);

	spinner.setAttribute('hidden', '');
	abort.setAttribute('hidden', '');

	if (!download) {
		warn.removeAttribute('hidden');
	}
}

