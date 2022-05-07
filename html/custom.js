
// Main menu hover
function menuselect(id) {
	const icon = document.getElementById(id);
	icon.style.color = '#dc3545';
}

function menuleave(id) {
	const icon = document.getElementById(id);
	icon.style.color = '#212529';
}

// For data "processed" AJAX
function ajax(id,type) {
	const xhttp = new XMLHttpRequest();
	xhttp.onload = function() {
		if (this.status == 200) {
			document.getElementById(id).style.display = "none";
		}
	}
	xhttp.open("GET", "done.php?id=" + id + "&type=" + type, true);
	xhttp.send();
}
