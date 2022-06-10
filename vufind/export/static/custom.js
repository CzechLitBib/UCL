
// JS Fetch API

const spinner = document.getElementById("spinner");

function loadData(format) {
	spinner.removeAttribute('hidden');
	fetch('/export?format=' + format)
	.then(response => response.json())
	.then(data => {
		spinner.setAttribute('hidden', '');
		console.log(data)
	});
}

