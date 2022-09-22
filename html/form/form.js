
// Customize selection 
function on_load() {
	document.getElementById("article-block").style.display = "block";
	document.getElementById("article-book-block").style.display = "block";
	document.getElementById("chapter-block").style.display = "none";
	document.getElementById("chapter-book-block").style.display = "none";
	document.getElementById("other-block").style.display = "none";
}

function format_load() {
	if (document.getElementById('article').checked) {
		on_load();
	}
	if (document.getElementById('chapter').checked) {
		document.getElementById("article-block").style.display = "none";
		document.getElementById("article-book-block").style.display = "none";
		document.getElementById("chapter-block").style.display = "block";
		document.getElementById("chapter-book-block").style.display = "block";
		document.getElementById("other-block").style.display = "none";
	}
	if (document.getElementById('book').checked) {
		document.getElementById("article-block").style.display = "none";
		document.getElementById("article-book-block").style.display = "block";
		document.getElementById("chapter-block").style.display = "none";
		document.getElementById("chapter-book-block").style.display = "block";
		document.getElementById("other-block").style.display = "none";
	}
	if (document.getElementById('study').checked) {
		document.getElementById("article-block").style.display = "none";
		document.getElementById("article-book-block").style.display = "none";
		document.getElementById("chapter-block").style.display = "block";
		document.getElementById("chapter-book-block").style.display = "block";
		document.getElementById("other-block").style.display = "none";
	}
	if (document.getElementById('other').checked) {
		document.getElementById("article-block").style.display = "none";
		document.getElementById("article-book-block").style.display = "block";
		document.getElementById("chapter-block").style.display = "none";
		document.getElementById("chapter-book-block").style.display = "none";
		document.getElementById("other-block").style.display = "block";
	}
}

// Example starter JavaScript for disabling form submissions if there are invalid fields
(function () {
  'use strict'

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.querySelectorAll('.needs-validation')

  // Loop over them and prevent submission
  Array.prototype.slice.call(forms)
    .forEach(function (form) {
      form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }

        form.classList.add('was-validated')
      }, false)
    })
})()
