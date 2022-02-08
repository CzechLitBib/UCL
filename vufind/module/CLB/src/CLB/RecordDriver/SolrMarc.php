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

		// This is all the collected data:
		$retval = [];

		// Try each MARC field one at a time:
		foreach ($subjectFields as $field => $fieldType) {
			// Do we have any results for the current field?  If not, try the next.
			$results = $this->getMarcReader()->getFields($field);
			if (!$results) {
				continue;
			}

			// If we got here, we found results -- let's loop through them.
			foreach ($results as $result) {
				// Start an array for holding the chunks of the current heading:
				$current = [];

				// Get all the chunks and collect them together:
				foreach ($result['subfields'] as $subfield) {
					// Numeric subfields are for control purposes and should not be displayed:
					if (!is_numeric($subfield['code'])) {
						if ($subfield['code'] != 'x')  {
							$current[] = $subfield['data'];
						}	
					}
				}
				// If we found at least one chunk, add a heading to our result:
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

		// Remove duplicates and then send back everything we collected:
		return array_map(
			'unserialize',
			array_unique(array_map('serialize', $retval))
		);
	}

	public function supportsAjaxStatus() {// override AJAX ILS
		return False;
	}

	public function CLB_getIn(bool $one = False) {// IN
		if (in_array($this->fields['format'], array('Book','Book Chapter'))) {
			return CLB_getBookChapterInfo();
		} else {
			$number = False;
			$result = '';
		
			$resource = isset($this->fields['article_resource_txt_mv']) ? $this->fields['article_resource_txt_mv'] : [];
			$related = isset($this->fields['article_resource_related_str_mv']) ? $this->fields['article_resource_related_str_mv'] : '';
			$issn = isset($this->fields['article_issn_str']) ? $this->fields['article_issn_str'] : '';
			$isbn = isset($this->fields['article_isbn_str_mv']) ? $this->fields['article_isbn_str_mv'] : '';
	
			for ($i=0; $i < count($resource); $i++) {
				if (empty($related[$i])) continue;
		
				if(!empty($resource[$i])) { $result .= "<a href='https://vufind.ucl.cas.cz/Search/Results?lookfor=" . urlencode($resource[$i]) . "&amp;type=ArticleResource'>" . $resource[$i] . "</a>"; }
		
				if (!$number) {
 					if ($issn) {
						$result .= ". -- ISSN <a href='https://vufind.ucl.cas.cz/Search/Results?lookfor=" . urlencode($issn) . "&amp;type=ISN'>" . $issn . "</a>";
					} elseif ($isbn) {
						foreach($isbn as $isn) {
							$result .= ". -- ISBN <a href='https://vufind.ucl.cas.cz/Search/Results?lookfor=" . urlencode($isn) . "&amp;type=ISN'>" . $isn . "</a>";
						}
					}
					$number = True;
				}
			
				$result .=  '. -- ' . $related[$i] . '<br>';
				if ($one) break;
      			}
			return $result;
	 	}
	}

	public function CLB_getBookChapterInfo() {// CHAPTER INFO
		$resource = isset($this->fields['article_resource_str_mv']) ? $this->fields['article_resource_str_mv'] : '';
		$title = isset($this->fields['related_doc_title_str_mv']) ? $this->fields['related_doc_title_str_mv'] : '';
		$data = '';

		$text1 = isset($resource[0]) ? trim(substr($resource[0], 0 , 10)) : '';
		$text2 = isset($title[0]) ? trim(substr($title[0], 0 , 10)) : '';
		if (!($text1 == $text2)) {
			return $this->CLB_getIn(True);	
		} else {
			$result2 = isset($this->fields['an_index_str_mv']) ? $this->fields['an_index_str_mv'] : '';
			# deal with wrong order of 737 and 787 values
			# TODO this can be removed if there will not be needed those fields
			if (!is_array($result2)) {
				$result3[] = $result2;
				array_push($result3, " ");
				$result2 = $result3;			
			} else {
				$odd = array();
				$even = array();
				$both = array(&$even, &$odd);
				array_walk($result2, function($v, $k) use ($both) { $both[$k % 2][] = $v; });
				$count=round(count($result2)/2);
				for ($i=0; $i < $count; $i++) {
					if (array_key_exists($i, $odd)) { $result3[] = $odd[$i]; } 
					if (array_key_exists($i, $odd)) { $result3[] = $even[$i]; }
				}
			}
			$data = $result3[0] . ", " . $result3[1];
			$data = str_replace("-- .", "", $data);
			$data = str_replace(". . ", ". ", $data);
			$data = str_replace(".. ", ". ", $data);
			$data = str_replace(". ,", ", ", $data);
			$data = str_replace("[], :","", $data);
			$data = str_replace(',  ,', ', ', $data);
			$data = str_replace(',  , ', ', ', $data);
			$data = str_replace(', , : ,  ', ', ', $data);
			$data = str_replace(', , ,  ', ', ', $data);
			$data = ltrim($data, "-- , ,");
			$data = ltrim($data, ". ");
			$data = ltrim($data, ": ");
			$data = rtrim($data, " -- , ");
			return $data;
		}
	}

	public function CLB_getRelated() {// RELATED
		$rel = isset($this->fields['related_doc_txt_mv']) ? $this->fields['related_doc_txt_mv'] : '';
		$data = '';

		if (!empty($rel)) {
			for ($i=0; $i < count($rel); $i++) {
				$name = $piece = "";
				$rel[$i] = str_replace("-- .", "", $rel[$i]);
				$rel[$i] = str_replace("-- () ", "", $rel[$i]);
				$rel[$i] = str_replace("-- [", "[", $rel[$i]);
				$rel[$i] = ltrim($rel[$i], ' : ');
				$rel[$i] = ltrim($rel[$i], '. ');
				$rel[$i] = str_replace("--  --", "--", $rel[$i]);
				$rel[$i] = str_replace('. -- [] ', '', $rel[$i]);
				$rel[$i] = str_replace(".    --    []", "", $rel[$i]);
				$rel[$i] = str_replace("    --    []", "", $rel[$i]);
				$rel[$i] = str_replace(". .", ". ", $rel[$i]);
				$rel[$i] = str_replace("..", ". ", $rel[$i]);

				$partArray = Array();
				$partArray = explode("XGRXG",$rel[$i]);
				$pieceLink = "<a href='https://vufind.ucl.cas.cz/Search/Results?join=AND&lookfor0[]=" . urlencode($partArray[0]) . "&type0[]=LinkedResource&bool0[]=AND'>" . $partArray[0] . "</a>";
				$data .= $pieceLink . implode("",$partArray) . '<br>';
			}
		}

		if (strlen($data) < 116) { $data = ''; } //?
		$data = ltrim($data, '. ');
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
		$genre = isset($this->fields['genre_str_mv']) ? $this->fields['genre_str_mv'] : '';
		$data = [];

		if (!empty($genre)) {
			foreach ($genre as $item) { 
				$data[] = "<a href='https://vufind.ucl.cas.cz/Search/Results?lookfor=" . urlencode($item) . "&amp;type=Genre'>" . $item . "</a>";
			}
		}
		return implode(", ", $data);
	}


	public function CLB_getCitation() { // CITATION
		$citation = isset($this->fields['citation_txt_mv']) ? $this->fields['citation_txt_mv'] : '';
		$text='';
		if (!empty($citation)) { $text = implode(',',$citation); }
		return $text;
	}

	public function CLB_getResponsibility() { // RESPONSIBILITY
		return isset($this->fields['responsibility_str_mv']) ? implode(", ", $this->fields['responsibility_str_mv']) : '';
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

	public function CLB_getCreationDate() { // COUNTRY
		if (isset($this->fields['record_creation_str_mv'])) {
			return preg_replace('/(\d{2})(\d{2})(\d{2})/','\3.\2.20\1', $this->fields['record_creation_str_mv']);
		}
		return [];
	}

	public function CLB_getEditDate() { // COUNTRY
		if (isset($this->fields['record_change_str_mv'])) {
			return preg_replace('/(\d{4})(\d{2})(\d{2})/', '\3.\2.\1', $this->fields['record_change_str_mv']);
		}
		return [];
	}

	public function CLB_getExcerptor() { // COUNTRY
		return isset($this->fields['processor_txt_mv']) ? $this->fields['processor_txt_mv'] : [];
	}

}

