<?php
ini_set('memory_limit', '512M');
set_time_limit(600);

require 'vendor/autoload.php';
$conn = array();
$conn['hosts']  = ['http://220.133.19.103:9200/'];

$client = new Elasticsearch\Client($conn);
/*
$params['index'] = 'twitter2';
$params['type']  = 'retweet';
$params['id']  = '579196299343343616';
$result = $client->get($params);
print_r($result['_source']['entities']);

*/

$params = array(
    "search_type" => "scan",    // use search_type=scan
    "scroll" => "5s",          // how long between scroll requests. should be small!
    "size" => 500,               // how many results *per shard* you want back
    "index" => "ptt",
	"type" => 'reply',
    "body" => array(
        "query" => array(
            "match_all" => array()
        )
    )
);
file_put_contents('data/reply','');

$docs = $client->search($params);   // Execute the search
$scroll_id = $docs['_scroll_id'];   // The response will contain no results, just a _scroll_id
//print_r($docs );
//exit;
// Now we loop until the scroll "cursors" are exhausted

$buf = [];
while (\true) {	
    // Execute a Scroll request
    $response = $client->scroll(
        array(
            "scroll_id" => $scroll_id,  //...using our previously obtained _scroll_id
            "scroll" => "5s"           // and the same timeout window
        )
    );
	
    // Check to see if we got any search hits from the scroll
    if (count($response['hits']['hits']) > 0) {
        // If yes, Do Work Here		
		
		foreach($response['hits']['hits'] as $result){	

			$source = $result['_source'];
		
			file_put_contents('data/reply',join("\t",$source)."\r\n",FILE_APPEND);

			unset($source);						
		}		
		//exit;
        // Get new scroll_id
        // Must always refresh your _scroll_id!  It can change sometimes
        $scroll_id = $response['_scroll_id'];
    } else {
        // No results, scroll cursor is empty.  You've exported all the data
        break;
    }
}

?>
