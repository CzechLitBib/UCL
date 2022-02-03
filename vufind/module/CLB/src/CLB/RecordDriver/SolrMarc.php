<?php

namespace CLB\RecordDriver;

//use Feature\MarcAdvancedTrait;

class SolrMarc extends \VuFind\RecordDriver\SolrMarc
{
	// override Versions = $this->displayVersions 

	public function supportsAjaxStatus() {// override AJAX ILS
		return False;
	}

	public function CLB_getIn(bool $one = False) {// IN
		if preg_match('^Kapitola)?.*(knihy)?$', $this->getFormatList()) {
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
						foreach($isbn as $i) { $result .= ". -- ISBN <a href='https://vufind.ucl.cas.cz/Search/Results?lookfor=" . urlencode($i) . "&amp;type=ISN'>" . $i . "</a>"; }
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
			$count = count($ex);
			for ($i=0; $i < $ex; $i++) {
				$ex[$i] = str_replace("pocet zaznamu ,", "", $ex[$i]);
				$ex[$i] = rtrim($ex[$i], ", ");
				$ex[$i] = ltrim($ex[$i], ", ");
				$ex[$i] = str_replace("pocet zaznamu", "počet záznamů:", $ex[$i]);
			}
			$data = implode("<br> ", $ex);
		}
		return $data;
	}

	public function CLB_getAnnotation() { // ANNOTATION
		$annotation = isset($this->fields['annotation_txt_mv']) ? $this->fields['annotation_txt_mv'] : '';
		$break = " ";
		$limit = 150;
		$pad = "...";
		$data = '';

		if (!empty($annotation)) {
			$data = implode(',',$annotation);
			if (strlen($data) > 150) {
				if(false !== ($breakpoint = strpos($data, $break, $limit))) {
					if($breakpoint < strlen($data) - 1) {
						$text = substr($data, 0, $breakpoint) . $pad;
					}
				}
			}
		}
		return $data;
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
		$text=''
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
}

