

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
