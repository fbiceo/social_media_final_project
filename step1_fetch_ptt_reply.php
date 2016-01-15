<?php
ini_set('memory_limit', '512M');
set_time_limit(0);

require 'vendor/autoload.php';
$conn = array();
$conn['hosts']  = ['http://220.133.19.103:9200/'];

$client = new Elasticsearch\Client($conn);


$reply_limit = 100;
$data = file('data/topic_step1_'.$reply_limit);
	

foreach($data as $key => $line){
	$topic_id = trim(explode("\t",$line)[0]);
	if(strlen($topic_id) < 10){
		continue;
	}
	$topic_url = trim(explode("\t",$line)[6]);
	file_put_contents('data/ptt/'.$topic_id,fetchPTT($topic_url));	
	echo $key.'___'.$topic_id."\n";
	sleep(rand(2,5));	
}

function fetchPTT($url){
	$c = curl_init($url);
	curl_setopt($c, CURLOPT_VERBOSE, 1);	
	curl_setopt($c, CURLOPT_COOKIE, 'over18=1');
	curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	$page = curl_exec($c);	
	curl_close($c);	
	return $page;
}

?>
