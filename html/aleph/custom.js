
// add query
function last_query_id() {
	queries = document.getElementsByName('query[]');
	last = queries[queries.length - 1].id;
	id = last.match('\\d+')
	if (Array.isArray(id) && id.length) {
		return Number(id[0]) + 1
	} else {
		return null;
	}
}

function add_query() {
	id = String(last_query_id());
	query = document.createDocumentFragment();

	row = document.createElement('div');
	row.className = 'row mt-2 gx-0 justify-content-center';
	col = document.createElement('div');
	col.className = 'col';
	form = document.createElement('div');
	form.className = 'form-floating';
	input = document.createElement('input');
	input.type = 'text';
	input.className = 'form-control';
	input.id = 'query' + id;
	input.name = 'query[]';
	input.value = '';
	label = document.createElement('label');
	label.htmlFor= 'query' + id;
	label.textContent = 'Podmínka';

	form.appendChild(input);
	form.appendChild(label);
	col.appendChild(form);
	row.appendChild(col);
	query.appendChild(row);

	document.getElementById('query-group').appendChild(query);
}

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
				!clo.includes(checks[i].value.replace("field_", "")) &&
				!clo.includes(checks[i].value.replace(/subfield_(.+)-./, "$1")) &&
				!clo.includes(checks[i].value.replace(/local_(LDR|008).+/, "$1"))
			) { 
				checks[i].disabled = true;
				checks[i].checked = false;
			}
		}
	}
}

