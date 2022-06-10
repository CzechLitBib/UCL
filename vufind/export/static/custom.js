
// JS Fetch API

const spinner = document.getElementById("spinner");

async function check_status() {
	return await fetch('/export?status',{
		method:"GET",
	})
	.then(response => response)
	.then(data => {
		console.log(data)
	});
}

async function progress() {
	spinner.removeAttribute('hidden');
	const f = await this.check_status();
	spinner.setAttribute('hidden', '');
	console.log(f)
}
