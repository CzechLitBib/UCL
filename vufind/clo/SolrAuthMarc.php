<?php

namespace CLB\RecordDriver;

class SolrAuthMarc extends \VuFind\RecordDriver\SolrAuthMarc
{

	public function CLB_getBirth() { // BIRTH

		$date='';
		$place='';

		if (isset($this->fields['birth_date_str'])) {
			if ($this->fields['birth_date_str'] != '00000000') {
				$year = preg_replace('/(\d{4})(\d{2})(\d{2})/', '$1', $this->fields['birth_date_str']);
				$month = preg_replace('/(\d{4})(\d{2})(\d{2})/', '$2', $this->fields['birth_date_str']);
				$day = preg_replace('/(\d{4})(\d{2})(\d{2})/', '$3', $this->fields['birth_date_str']);
			
				$date = preg_replace('/0(\d)/','$1',$day) . '. ' . preg_replace('/0(\d)/','$1',$month) . '. ' . $year;
			}
		}

		if (isset($this->fields['birth_place'])) { $place = $this->fields['birth_place']; }

		if (!empty($date) or !empty($place)) {
			return trim('* ' . $date . ' ' . $place);
		}

		return '';
	}

	public function CLB_getDeath() { // DEATH

		$date='';
		$place='';

		if (isset($this->fields['death_date_str'])) {
			if ($this->fields['death_date_str'] != '00000000') {
				$year = preg_replace('/(\d{4})(\d{2})(\d{2})/', '$1', $this->fields['death_date_str']);
				$month = preg_replace('/(\d{4})(\d{2})(\d{2})/', '$2', $this->fields['death_date_str']);
				$day = preg_replace('/(\d{4})(\d{2})(\d{2})/', '$3', $this->fields['death_date_str']);
		
				$date = preg_replace('/0(\d)/','$1',$day) . '. ' . preg_replace('/0(\d)/','$1',$month) . '. ' . $year;
			}
		}

		if (isset($this->fields['death_place'])) { $place = $this->fields['death_place']; }

		if (!empty($date) or !empty($place)) {
			return trim('† ' . $date . ' ' . $place);
		}

		return '';
	}

	public function CLB_getBio() { // BIO
		return $this->getFieldArray('678', ['a']);
	}

}

