<?php

$url='http://localhost:8983/solr/core/select';

$wt='wt=csv';
$csv_separator='csv.separator=' . urlencode(';');
$csv_mv_separator='csv.mv.separator=' . urlencode('#');

$q_op='q.op=AND';
$q='q=' . urlencode('tag_100:*');
$fl='fl=' . urlencode('id,tag_100');

$rows='rows=10';

$params=array($csv_separator, $csv_mv_separator, $fl, $q_op, $q, $rows, $wt);

$request=$url . '?' . implode('&', $params);

header('Content-type: application/octet-stream; charset=UTF-8');
header('Content-disposition: attachment;filename=' . 'test.csv');

$opts = array('http'=>array('method'=>'GET'));

$context = stream_context_create($opts);

$fp = fopen($request, 'r', false, $context);

if ($fp) { 
	while(!feof($fp)) {
		$buffer = fread($fp, 2048);
		print $buffer;
	}
}

fclose($fp);

?>
