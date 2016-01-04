<?php
ini_set('memory_limit', '512M');
set_time_limit(600);

require 'vendor/autoload.php';
$conn = array();
$conn['hosts']  = ['http://220.133.19.103:9200/'];

$client = new Elasticsearch\Client($conn);


	$reply_limit = 1000;
	$data = file('data/topic_step1_'.$reply_limit);
		
	$relation = [];	
		
	foreach($data as $line){
		$topic_id = explode("\t",$line)[0];
		$params['index'] = 'ptt';
		$params['type']  = 'reply';
		$params['size']  = '99999';
		$params['body']['query']['match']['TopicId'] = $topic_id;

		$results = $client->search($params);
		$total = $results['hits']['total'];
		$replys = $results['hits']['hits'];
		//print_r($replys);
		for($i=0;$i<$total-1;$i++){
			for($j=1;$j<$total;$j++){
				$author1 = $replys[$i]['_source']['Author'];
				$author2 = $replys[$j]['_source']['Author'];
				@$relation[$author1.",".$author2]++;
				@$relation[$author2.",".$author1]++;
				//echo $i.'=>'.$author1.','.$j.'=>'.$author2."\r\n";
			}
		}
		unset($results,$replys);
		break;
	}
	arsort($relation);
	//print_r($relation);
	//exit;
	$filename = 'data/relation_'.$reply_limit;
	file_put_contents($filename,'');
	foreach($relation as $user=>$hits){
		file_put_contents($filename,$user."\t".$hits."\r\n",FILE_APPEND);		
	}
	//file_put_contents('data/relation_'.$reply_limit,join("\r\n",$relation));
?>
