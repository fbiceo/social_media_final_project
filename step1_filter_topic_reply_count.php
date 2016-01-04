<?php
	$reply_limit = 1000;
	file_put_contents('data/topic_step1_'.$reply_limit,'');
	
	$data = file('data/topic');	
	foreach($data as $line){
		$topic = explode("\t",$line);
		if(($topic[2]+$topic[3]+$topic[4]) > $reply_limit){
			file_put_contents('data/topic_step1_'.$reply_limit,join("\t",$topic)."\r\n",FILE_APPEND);		
		}
	}
?>