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
			'648' => 'chronological',
			'650' => 'topic',
			'651' => 'geographic',
			'653' => '',
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


	public function CLB_getDeduplicatedPrimaryAuthors() {// CUSTOM PRIMARY AUTHORS
		$authors = [];
		$authors['primary'] = $this->getAuthorDataFields('primary', ['role']);

		$dedup_data = function (&$array) {
			foreach ($array as $author => $data) {
				foreach ($data as $field => $values) {
					if (is_array($values)) {
						$array[$author][$field] = array_unique($values);
					}
				}
			}
		};

		$dedup_data($authors['primary']);
		return $authors;
	}

	public function CLB_getSubfields(string $field, array $subfields) {// CUSTOM SUBFIELD READER
		$data = [];
		$fields = $this->getMarcReader()->getFields($field);
		foreach($fields as $fd) {
			$batch = [];
			foreach ($fd['subfields'] as $subfield) {
				if (in_array( $subfield['code'], $subfields)) {
					$batch[$subfield['code']] = $subfield['data'];
				}
			}
			$data[] = $batch;
		}
		return $data;
	}

	public function CLB_getInfo() {// INFO
		$data = [];
		$title = isset($this->fields['article_resource_str_mv']) ? $this->fields['article_resource_str_mv'] : [];# 773t
		$related = $this->getFieldArray('773', ['g']);
	
		if ($this->fields['format'] == 'Book Chapter') {
			$is_chapter = False;
			$resources = $this->getFieldArray('787',['t']);
			foreach($resources as $resource) {
				if ($resource['subfields']['code']['t'] == $title) { $is_chapter = True; }
			}
			if ($is_chapter) {
				$sub = $this->CLB_getSubfields('787', ['a', 't', 'd']);
				return $data[] = [
					'resource' => $title,
					'sub' => $sub,
					'related' => $related
				];# $a. $t. $d, $g
			}
		}
	
		$sub = $this->CLB_getSubfields('773', ['x', 'z']);
		return $data[] = [
			'resource' => $title,
			'sub' => $sub,
			'related' => $related
		];
	}

	public function CLB_getRelated() {// RELATED
		$data = [];
		$detail = isset($this->fields['related_doc_detail_str_mv']) ? $this->fields['related_doc_detail_str_mv'] : [];# 630alps
		$author = isset($this->fields['related_doc_author_str_mv']) ? $this->fields['related_doc_author_str_mv'] : [];# 787at
		$sub = $this->CLB_getSubfields('787', ['n', 'b', 'd', 'k', 'h', 'x', 'z', '4']);

		return $data[] = [
			'detail' => $detail,
			'author' => $author,
			'sub' => $sub
		];
	}

	public function CLB_getActualExcerption() { // ACTUAL EXCERPTION
		return $this->CLB_getSubfields('912', ['q', 'r', 'm', 'n', 'z']);
	}

	public function CLB_getFinishedExcerption() { // FINISHED EXCERPTION
		return $this->CLB_getSubfields('913', ['q', 'r', 'm', 'n', 'z']);
	}

	public function CLB_getAnnotation(bool $full = True) { // ANNOTATION
		$annotation = $this->getSummary();
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
		return $this->CLB_getAnnotation(False);
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
		$statement = $this->getTitleStatement();# 245c
		if (
			!empty($this->getMarcReader()->getField('700'))
			or !empty($this->getMarcReader()->getField('710'))
			or str_contains($statement, '=')
		) {
			return $statement;
		}
		return '';
	}

	public function CLB_getMoreInfo() { // INFO
		return isset($this->fields['more_info_str_mv']) ? $this->fields['more_info_str_mv'] : [];
	}

	public function CLB_getConspectGroup() { // CONSPEKT
		return isset($this->fields['conspect_group_str_mv']) ? $this->fields['conspect_group_str_mv'] : [];
	}

	public function CLB_getExcerptionPeriod() { // EXCERPTION PERIOD
		return $this->getFieldArray('911', ['r']);
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

