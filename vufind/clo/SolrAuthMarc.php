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

	public function CLB_getUccupation() { // OCCUPATION
		return isset($this->fields['occupation']) ? $this->fields['occupation'] : [];
	}

	public function CLB_getActivity() { // ACTIVITY
		return isset($this->fields['field_of_activity']) ? $this->fields['field_of_activity'] : [];
	}

	public function CLB_getAssocGroup() { // ASSOCIATED GROUP
		return isset($this->fields['associated_group_str_mv']) ? $this->fields['associated_group_str_mv'] : [];
	}

	public function CLB_getHonorific() { // HONORIFIC
		return isset($this->fields['honorific_str_mv']) ? $this->fields['honorific_str_mv'] : [];
	}

	public function CLB_getGender() { // GENDER
		return isset($this->fields['gender']) ? $this->fields['gender'] : [];
	}

	public function CLB_getLanguageFrom() { // LANGUAGE FROM
		return isset($this->fields['language_from_str_mv']) ? $this->fields['language_from_str_mv'] : [];
	}

	public function CLB_getLanguageTo() { // LANGUAGE TO
		return isset($this->fields['language_from_to_mv']) ? $this->fields['language_from_to_mv'] : [];
	}

	public function CLB_getRelatedPlace() { // RELALED PLACE
		return isset($this->fields['related_place']) ? $this->fields['related_place'] : [];
	}

	public function CLB_getCountry() { // COUNTRY
		return isset($this->fields['country']) ? $this->fields['country'] : [];
	}

	public function CLB_getNote() { // COUNTRY
		return isset($this->fields['note_txt_mv']) ? $this->fields['note_txt_mv'] : [];
	}

}

