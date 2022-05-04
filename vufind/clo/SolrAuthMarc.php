<?php

namespace CLB\RecordDriver;

class SolrAuthMarc extends \VuFind\RecordDriver\SolrAuthMarc
{

	public function CLB_getBirth() { // BIRTH

		$date='';
		$place='';

		if (isset($this->fields['birth_full_date'])) {
			$date = date_format(date_create_from_format('Y-m-d\TH:i:s\Z', $this->fields['birth_full_date']), 'j. n. Y');
		}

		if (isset($this->fields['birth_place'])) { $place = $this->fields['birth_place']; }

		if (!empty($date) or !($empty($place)) {
			return trim('*' . $date . ' ' . $place);
		}

		return '';
	}

	public function CLB_getDeath() { // DEATH

		$date='';
		$place='';

		if (isset($this->fields['death_full_date'])) {
			$date = date_format(date_create_from_format('Y-m-d\TH:i:s\Z', $this->fields['death_full_date']), 'j. n. Y');
		}

		if (isset($this->fields['death_place'])) { $place = $this->fields['death_place']; }

		if (!empty($date) or !($empty($place)) {
			return trim('*' . $date . ' ' . $place);
		}

		return '';
	}

}

