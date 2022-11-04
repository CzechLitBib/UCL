
// mail requirement
function mail_req(req) {
	if (req) {
		document.getElementById("email").required = true;
	} else {
		document.getElementById("email").required = false;
	}
}

// type selection

function type_load() {
	if (document.getElementById('fulltext').checked) {
		document.getElementById("fulltext-block").style.display = "block";
		document.getElementById("biblio-block").style.display = "none";
	} else {
		document.getElementById("fulltext-block").style.display = "none";
		document.getElementById("biblio-block").style.display = "block";
	}
}

// default selection 
function on_load() {

	document.getElementById("fulltext").checked = true;
	document.getElementById("article").checked = true;

	document.getElementById("fulltext-block").style.display = "block";
	document.getElementById("biblio-block").style.display = "none";

	document.getElementById("article-block").style.display = "block";
	document.getElementById("article-book-block").style.display = "block";
	document.getElementById("chapter-block").style.display = "none";
	document.getElementById("chapter-book-block").style.display = "none";
	document.getElementById("other-block").style.display = "none";
	document.getElementById("page-block").style.display = "none";
}

// format selection 
function format_load() {
	if (document.getElementById('article').checked) {
		document.getElementById("article-block").style.display = "block";
		document.getElementById("article-book-block").style.display = "block";
		document.getElementById("chapter-block").style.display = "none";
		document.getElementById("chapter-book-block").style.display = "none";
		document.getElementById("other-block").style.display = "none";
		document.getElementById("page-block").style.display = "none";
	}
	if (document.getElementById('chapter').checked) {
		document.getElementById("article-block").style.display = "none";
		document.getElementById("article-book-block").style.display = "none";
		document.getElementById("chapter-block").style.display = "block";
		document.getElementById("chapter-book-block").style.display = "block";
		document.getElementById("other-block").style.display = "none";
		document.getElementById("page-block").style.display = "block";
	}
	if (document.getElementById('book').checked) {
		document.getElementById("article-block").style.display = "none";
		document.getElementById("article-book-block").style.display = "block";
		document.getElementById("chapter-block").style.display = "none";
		document.getElementById("chapter-book-block").style.display = "block";
		document.getElementById("other-block").style.display = "none";
		document.getElementById("page-block").style.display = "none";
	}
	if (document.getElementById('study').checked) {
		document.getElementById("article-block").style.display = "none";
		document.getElementById("article-book-block").style.display = "none";
		document.getElementById("chapter-block").style.display = "block";
		document.getElementById("chapter-book-block").style.display = "block";
		document.getElementById("other-block").style.display = "none";
		document.getElementById("page-block").style.display = "block";
	}
	if (document.getElementById('other').checked) {
		document.getElementById("article-block").style.display = "none";
		document.getElementById("article-book-block").style.display = "block";
		document.getElementById("chapter-block").style.display = "none";
		document.getElementById("chapter-book-block").style.display = "none";
		document.getElementById("other-block").style.display = "block";
		document.getElementById("page-block").style.display = "none";
	}
}
