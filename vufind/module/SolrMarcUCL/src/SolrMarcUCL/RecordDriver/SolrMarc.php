<?php

namespace SolrMarcUCL\RecordDriver;

class SolrMarc extends \VuFind\RecordDriver\SolrMarc
{

  public function getMisto(bool $onlyOneResult = false) {
        $result = '';
        $result2 = isset($this->fields['article_resource_txt_mv']) ? $this->fields['article_resource_txt_mv'] : '';
        $result3 = isset($this->fields['article_issn_str']) ? $this->fields['article_issn_str'] : '';
        $result4 = isset($this->fields['article_resource_related_str_mv']) ? $this->fields['article_resource_related_str_mv'] : '';
        $placeIS = true;
        $count = count($result2);
        for ($i=0; $i < $count; $i++) {
                if (empty($result4[$i])) continue;
		if (!empty($result2[$i])) {
	                $result .= "<a href='https://vufind.ucl.cas.cz/Search/Results?lookfor=" . urlencode($result2[$i]) . "&amp;type=ArticleResource'>" . $result2[$i] . "</a>";
		}
# TODO this will not work when more isbn or issn fields, but probably there are no records like that
                if ($result3) {
                        if ($placeIS) {
                                $result .= ". -- ISSN <a href='https://vufind.ucl.cas.cz/Search/Results?lookfor=" . urlencode($result3) . "&amp;type=ISN'>" . $result3 . "</a>";
                                $placeIS = false;
                        }
                } elseif (isset($this->fields['article_isbn_str'])) {
                        if ($placeIS) {
                                $result .= ". -- ISBN <a href='https://vufind.ucl.cas.cz/Search/Results?lookfor=" . urlencode($this->fields['article_isbn_str']) . "&amp;type=ISN'>" . $this->fields['article_isbn_str'] . "</a>";
                                $placeIS = false;
                        }
                }

                if ($result4[$i]) {
                        $result .=  '. -- ' . $result4[$i] . '<br>';
                }
		if ($onlyOneResult) { 
			break; 
		}	
        }
        return $result;
  }

  public function getZanr() {
        $result = [];
        $result2 = isset($this->fields['genre_str_mv']) ? $this->fields['genre_str_mv'] : '';
	if (!empty($result2)) {
		foreach ($result2 as $item) { 
		        $result[] = "<a href='https://vufind.ucl.cas.cz/Search/Results?lookfor=" . urlencode($item) . "&amp;type=Genre'>" . $item . "</a>";
		}
	}
	return implode(", ", $result);
  }

    /**
   * Get the book chapter info
   *
   * @return string
   */
  public function getBookChapterInfo() {
# first we compare those fields and if their firt 10 chars are equal, we print getMisto else 
        $var773t = isset($this->fields['article_resource_str_mv']) ? $this->fields['article_resource_str_mv'] : '';
        $var787t = isset($this->fields['related_doc_title_str_mv']) ? $this->fields['related_doc_title_str_mv'] : '';
	$text1 = isset($var773t[0]) ? trim(substr($var773t[0], 0 , 10)) : '';
	$text2 = isset($var787t[0]) ? trim(substr($var787t[0], 0 , 10)) : '';
	if (!($text1 == $text2)) {
		return $this->getMisto(true);	
	} else {
		$result = "";
		$result2 = isset($this->fields['an_index_str_mv']) ? $this->fields['an_index_str_mv'] : '';
		# deal with wrong order of 737 and 787 values
		#TODO this can be removed if there will not be needed those fields
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
		$result = $result3[0] . ", " . $result3[1];
		$result = str_replace("-- .", "", $result);
		$result = str_replace(". . ", ". ", $result);
		$result = str_replace(".. ", ". ", $result);
		$result = str_replace(". ,", ", ", $result);
		$result = str_replace("[], :","", $result);
		$result = str_replace(',  ,', ', ', $result);
		$result = str_replace(',  , ', ', ', $result);
		$result = str_replace(', , : ,  ', ', ', $result);
		$result = str_replace(', , ,  ', ', ', $result);
		$result = ltrim($result, "-- , ,");
		$result = ltrim($result, ". ");
		$result = ltrim($result, ": ");
		$result = rtrim($result, " -- , ");
		return $result;
	}
  }

  public function getOdkazovaneDilo() {
  $result = '';
  $result2 = isset($this->fields['related_doc_txt_mv']) ? $this->fields['related_doc_txt_mv'] : '';
  if (!empty($result2)) {
  $count = count($result2);

  for ($i=0; $i < $count; $i++) {
    $name = $piece = "";
    $result2[$i] = str_replace("-- .", "", $result2[$i]);
    $result2[$i] = str_replace("-- () ", "", $result2[$i]);
    $result2[$i] = str_replace("-- [", "[", $result2[$i]);
    $result2[$i] = ltrim($result2[$i], ' : ');
    $result2[$i] = ltrim($result2[$i], '. ');
    $result2[$i] = str_replace("--  --", "--", $result2[$i]);
    $result2[$i] = str_replace('. -- [] ', '', $result2[$i]);
    $result2[$i] = str_replace(".    --    []", "", $result2[$i]);
    $result2[$i] = str_replace("    --    []", "", $result2[$i]);
    $result2[$i] = str_replace(". .", ". ", $result2[$i]);
    $result2[$i] = str_replace("..", ". ", $result2[$i]);

    $partArray = Array();
    $partArray = explode("XGRXG",$result2[$i]);

#    list($name, $piece, $rest) = explode(".",$result2[$i]);
#    $nameLink = "<a href='https://vufind.ucl.cas.cz/Search/Results?lookfor=" . urlencode($name) . "&amp;type=Author'>" . $name . "</a>";
#    $result2[$i] = str_replace($name, $nameLink, $result2[$i]);
#echo $result2[$i];
#    if (!empty($partArray[1])) {
	    $pieceLink = "<a href='https://vufind.ucl.cas.cz/Search/Results?join=AND&lookfor0[]=" . urlencode($partArray[0]) . "&type0[]=LinkedResource&bool0[]=AND'>" . $partArray[0] . "</a>";
#	    $result2[$i] = str_replace($piece, $pieceLink, $result2[$i]);
#	    $partArray = array_slice($partArray, 2); 
#    } else {
#	    $pieceLink = "<a href='https://vufind.ucl.cas.cz/Search/Results?lookfor=" . urlencode($partArray[0]) . "&amp;type=Subject'>" . $partArray[1] . "</a>";
            $partArray = array_slice($partArray, 1); 
#    }
#    echo "res: " . $result2[$i] . "name: " . $name . "link: " . $piece;
#    $result .= $result2[$i] . '<br>';
    $result .= $pieceLink . implode("",$partArray) . '<br>';
  }
  }
    if (strlen($result) < 116 ) {
      $result = "";
    } 

    $result = ltrim($result, '. ');
# echo(strlen($result)); 
  return $result;
 }

 public function getResponsibility() {
	$result = isset($this->fields['responsibility_str_mv']) ? implode(", ", $this->fields['responsibility_str_mv']) : '';
#	$result2 = implode(", ", $result);
	return $result;
 }

 public function getActualExcerption() {
# first we compare those fields and if their firt 10 chars are equal, we print getMisto else 
        $result= isset($this->fields['actual_excerption_txt_mv']) ? $this->fields['actual_excerption_txt_mv'] : '';

        if (!empty($result)) {
               $count = count($result);
                for ($i=0; $i < $count; $i++) {
                        $result[$i] = str_replace("pocet zaznamu ,", "", $result[$i]);
                        $result[$i] = rtrim($result[$i], ", ");
                        $result[$i] = ltrim($result[$i], ", ");
                        $result[$i] = str_replace("pocet zaznamu", "poet záznamů", $result[$i]);
                }

                $result2 = implode("<br> ", $result);
        } else {
                $result2 = "";
        }
        return $result2;
  }

  public function getFinishedExcerption() {
# first we compare those fields and if their firt 10 chars are equal, we print getMisto else 
        $result = isset($this->fields['finished_excerption_txt_mv']) ? $this->fields['finished_excerption_txt_mv'] : '';
        if (!empty($result)) {
               $count = count($result);
                for ($i=0; $i < $count; $i++) {
                        $result[$i] = str_replace("pocet zaznamu ,", "", $result[$i]);
                        $result[$i] = rtrim($result[$i], ", ");
                        $result[$i] = ltrim($result[$i], ", ");
                        $result[$i] = str_replace("pocet zaznamu", "počet záznamů:", $result[$i]);
                }
                $result2 = implode("<br> ", $result);
        }  else {
                $result2 = "";
        }

        return $result2;

  }

# public function getAnnotation() {
#   $result = isset($this->fields['annotation_txt_mv']) ? $this->fields['annotation_txt_mv'] : '';
#   if (!empty($result)) {
#        $string = implode(',',$result);
#        $result_string = (strlen($string) > 150) ? substr($string,0,150).'...' : $string;
#   }
#   return  $result_string;#implode("<br> ", $result);
# }

 public function getAnnotation() {
   $result = isset($this->fields['annotation_txt_mv']) ? $this->fields['annotation_txt_mv'] : '';
   $break = " ";
   $limit = 150;
   $pad = "...";
   if (!empty($result)) {
        $string = implode(',',$result);
    if (strlen($string) > 150) {
        if(false !== ($breakpoint = strpos($string, $break, $limit))) {
          if($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
          }
        }
   }
   } else {
        $string = "";
   }

   return  $string;
 }



 public function getCitation() {
   $result = isset($this->fields['citation_txt_mv']) ? $this->fields['citation_txt_mv'] : '';
   if (!empty($result)) {
        $string = implode(',',$result);
#        $result_string = (strlen($string) > 150) ? substr($string,0,150).'...' : $string;
   } else {
	$string = "";
   }
   return  $string;
 }

}

