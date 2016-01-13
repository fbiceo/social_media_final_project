<?php
ini_set('memory_limit', '512M');
set_time_limit(0);

require 'vendor/autoload.php';
$conn = array();
$conn['hosts']  = ['http://220.133.19.103:9200/'];

$client = new Elasticsearch\Client($conn);


	$reply_limit = 100;
	$data = file('data/topic_step1_'.$reply_limit);
		
	$filename = 'data/reply_'.$reply_limit.'_authors';
	file_put_contents($filename,'');
	
	foreach($data as $key=>$line){
		$topic_id = explode("\t",$line)[0];
		if(strlen($topic_id) < 10){
			continue;
		}
		$params['index'] = 'ptt';
		$params['type']  = 'reply';
		$params['size']  = '99999';
		$params['body']['query']['match']['TopicId'] = $topic_id;

		$results = $client->search($params);
		$total = $results['hits']['total'];
		$replys = $results['hits']['hits'];
		echo $key.'____'.$topic_id."\n";
		$author_row = [];
		foreach($replys as $v){
			$author_row[] = trim($v['_source']['Author']);
		}		
		if(count($author_row) > 0){			
			file_put_contents($filename,join(',',$author_row)."\n",FILE_APPEND);		
		}
		
		unset($results,$replys,$author_row);
		//break;
	}
	
	
	//foreach($author_file as $row){
	//	file_put_contents($filename,$row,FILE_APPEND);		
	//}
	//file_put_contents('data/relation_'.$reply_limit,join("\r\n",$relation));
?>
