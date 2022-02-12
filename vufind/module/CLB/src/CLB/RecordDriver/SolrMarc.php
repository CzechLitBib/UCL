<?php

namespace CLB\RecordDriver;

class SolrMarc extends \VuFind\RecordDriver\SolrMarc
{

	// exclude 630/655/656; exclude 'x' subfield
	public function getAllSubjectHeadings($extended = false)
	{
		$subjectFields = [
			'600' => 'personal name',
			'610' => 'corporate name',
			'611' => 'meeting name',
			#'630' => 'uniform title',
			'648' => 'chronological',
			'650' => 'topic',
			'651' => 'geographic',
			'653' => '',
			#'655' => 'genre/form',
			#'656' => 'occupation'
		];

		$retval = [];

		foreach ($subjectFields as $field => $fieldType) {
			$results = $this->getMarcReader()->getFields($field);
			if (!$results) { continue; }

			foreach ($results as $result) {
				$current = [];

				foreach ($result['subfields'] as $subfield) {
					if (!is_numeric($subfield['code'])) {
						if ($subfield['code'] != 'x')  {
							$current[] = $subfield['data'];
						}	
					}
				}
		
				if (!empty($current)) {
					if ($extended) {
						$sourceIndicator = $result['i2'];
						$source = '';
						if (isset($this->subjectSources[$sourceIndicator])) {
							$source = $this->subjectSources[$sourceIndicator] ?? '';
						} else {
							$source = $this->getSubfield($result, '2');
						}
						$retval[] = [
							'heading' => $current,
							'type' => $fieldType,
							'source' => $source,
							'id' => $this->getSubfield($result, '0')
						];
					} else {
						$retval[] = $current;
					}
				}
			}
		}

		return array_map('unserialize', array_unique(array_map('serialize', $retval)));
	}

	public function supportsAjaxStatus() {// override AJAX ILS
		return False;
	}

	public function CLB_getIn() {// IN
		return '[opravuje se..]';
		if ($this->fields['format'] == 'Book Chapter') {
			return $this->CLB_getBookChapterInfo();
		}
	
		$data = array('url' => [],'issn' => [], 'isbn' => [], 'data'=> '');
		$resource = $this->getFieldArray('773', ['t', 'g', 'x', 'z'], false);

		foreach($resource as $field => $subfields) {
			if (!isset($field['g'])) {continue; } # skip missing 773g;
				# title link<a href='http://vufind2.ucl.cas.cz/Search/Results?lookfor=".."&amp;type=ArticleResource'>"
				# issn". -- ISSN <a href='http://vufind2.ucl.cas.cz/Search/Results?lookfor="..."&amp;type=ISN'>"
				# isbn [mv]". -- ISBN <a href='http://vufind2.ucl.cas.cz/Search/Results?lookfor=".."&amp;type=ISN'>" 
		}		# 'g :  . -- ' . $related[0] . '<br>';
		return $data;
	}

	public function CLB_getBookChapterInfo() {// CHAPTER INFO
		return '[opravuje se..]';
		$data = '';
		$resouce = $this->getFieldArray('773', ['t', 'g']);
		$an = $this->getFieldArray('787', ['a','t','d']);

		if ($resource['t'] != $an['t']) {
			return $this->CLB_getIn();
		} else {
			return $data = $an['a'] . '. ' . $an['t'] . '. ' . $an['d'] . ', ' . $resource['g']; 
		}
	}

	public function CLB_getRelated() {// RELATED
		$data = '';
		$link = '';
		$f1 = $this->getFieldArray('630', ['a', 'l', 'p', 's'], false);
		$f2 = $this->getFieldArray('787', ['a', 't', 'n', 'b', 'd', 'k', 'h', 'x', 'z', '4'], false);

		# link
		# $this->getMarcReader()->getSubfieldArray('630', ['a','l','p'])

		if (!empty($f1)) {
			$i=0;
			foreach($f1 as $subfield => $value) {
				$i == 0 ? $data .= trim($value, '.') : $data .= '. -- ' . trim($value, '.');
				$i++;
			}	
		}

		if (!empty($f2)) {
			$i=0;
			foreach($f2 as $subfield => $value) {
				$i == 0 ? $data .= trim($value, '.') : $data .= '. -- ' . trim($value, '.');
				$i++;
			}	
		}
		return $data;
	}

	public function CLB_getActualExcerption() { // ACTUAL EXCERPTION
		$ex = isset($this->fields['actual_excerption_txt_mv']) ? $this->fields['actual_excerption_txt_mv'] : '';
		$data = '';

		if (!empty($ex)) {
			for ($i=0; $i < count($ex); $i++) {
				$ex[$i] = str_replace("pocet zaznamu ,", "", $ex[$i]);
				$ex[$i] = rtrim($ex[$i], ", ");
				$ex[$i] = ltrim($ex[$i], ", ");
				$ex[$i] = str_replace("pocet zaznamu", "poet záznamů", $ex[$i]);
			}
			$data = implode("<br> ", $ex);
		}
		return $data;
	}

	public function CLB_getFinishedExcerption() { // FINISHED EXCERPTION
		$ex = isset($this->fields['finished_excerption_txt_mv']) ? $this->fields['finished_excerption_txt_mv'] : '';
		$data = '';

		if (!empty($ex)) {
		//	for ($i=0; $i < count($ex); $i++) {
		//		$ex[$i] = str_replace("pocet zaznamu ,", "", $ex[$i]);
		//		$ex[$i] = rtrim($ex[$i], ", ");
		//		$ex[$i] = ltrim($ex[$i], ", ");
		//		$ex[$i] = str_replace("pocet zaznamu", "počet záznamů:", $ex[$i]);
		//	}
			$data = implode("<br> ", $ex);
		}
		return $data;
	}

	public function CLB_getAnnotation(bool $full = True) { // ANNOTATION
		$annotation = isset($this->fields['annotation_txt_mv']) ? $this->fields['annotation_txt_mv'] : '';
		$data = '';

		if (!empty($annotation)) {
			$data = implode(',', $annotation);
			if (!$full and strlen($data) > 150) {
				if(false !== ($breakpoint = strpos($data, ' ', 150))) {
					if($breakpoint < strlen($data) - 1) {
						$data = substr($data, 0, $breakpoint) . '...';
					}
				}
			}
		}
		return $data;
	}

	public function CLB_getAnnotationShort() { // ANNOTATION - SHORT
		return  $this->CLB_getAnnotation(False);
	}

	public function CLB_getGenre() { // GENRE
		return isset($this->fields['genre_str_mv']) ? $this->fields['genre_str_mv'] : [];
	}

	public function CLB_getCitation() { // CITATION
		$citation = isset($this->fields['citation_txt_mv']) ? $this->fields['citation_txt_mv'] : [];
		$text='';
		if (!empty($citation)) { $text = implode(',',$citation); }
		return $text;
	}

	public function CLB_getResponsibility() { // RESPONSIBILITY
		$data = '';
		if (!empty($this->getMarcReader()->getField('700')) or !empty($this->getMarcReader()->getField('710'))) {
			if (strpos('=', $this->getTitleStatement(), 0)) {
				$data = $this->getTitleStatement();# 245c
			}
		}
		return $data;
	}

	public function CLB_getMoreInfo() { // INFO
		return isset($this->fields['more_info_str_mv']) ? $this->fields['more_info_str_mv'] : [];
	}

	public function CLB_getConspectGroup() { // CONSPEKT
		return isset($this->fields['conspect_group_str_mv']) ? $this->fields['conspect_group_str_mv'] : [];
	}

	public function CLB_getExcerptionPeriod() { // EXCERPTION PERIOD
		return isset($this->fields['excerption_period_str_mv']) ? $this->fields['excerption_period_str_mv'] : [];
	}

	public function CLB_getCountry() { // COUNTRY
		return isset($this->fields['country_str_mv']) ? $this->fields['country_str_mv'] : [];
	}

	public function CLB_getCreationDate() { // RECORD CREATION DATE
		if (isset($this->fields['record_creation_date'])) {
			return date_format(date_create_from_format('Y-m-d\TH:i:s\Z', $this->fields['record_creation_date']), 'j. n. Y');
		}
		return '';
	}

	public function CLB_getEditDate() { // RECORD EDIT DATE
		if (isset($this->fields['record_change_date'])) {
			return date_format(date_create_from_format('Y-m-d\TH:i:s\Z', $this->fields['record_change_date']), 'j. n. Y');
		}
		return '';
	}

	public function CLB_getExcerptor() { // EXCERPTOR
		return isset($this->fields['processor_txt_mv']) ? $this->fields['processor_txt_mv'] : '';
	}

}

