
// modal
modal = new bootstrap.Modal(document.getElementById('help'));
function help() {
	modal.toggle();
}

// core selector
function get_selection(core) {
	const clo = [
		'001','003','005','008','024','040','046','080',
		'100','368','370','371','372','373','374','375',
		'377','400','400','663','664','665','667','670',
		'675','678','680','682','856','906','961','962',
		'963','964','965','966','967','995','KON','LDR',
		'POS','POZ','VER'
	];

	checks = document.querySelectorAll('input[type=checkbox]');
	for (var i = 0; i < checks.length; i++) {
		checks[i].disabled = false;
		if (core == 'clo') {
			if (
				!clo.includes(checks[i].name.replace("field_", "")) &&
				!clo.includes(checks[i].name.replace(/subfield_(.+)-./, "$1")) &&
				!clo.includes(checks[i].name.replace(/local_(LDR|008).+/, "$1"))
			) { 
				checks[i].disabled = true;
				checks[i].checked = false;
			}
		}
	}
}

