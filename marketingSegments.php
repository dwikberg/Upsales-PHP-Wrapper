<?php

	require_once('upsales.php');
	header('Content-type: text/html; charset=utf-8');
	mb_http_output('UTF-8');

	// Display Lead Score grouped by Account

	$myUpsales = new Upsales();

	$params = '{"a":"score","c":"gt","v":0}&q={"a":"date","c":"gt","v":"2016-01-01"}';
	$events = $myUpsales->getEvents($params);

	$resultsArray = array();

	$howmany=0;
	foreach ($events["data"] as $event) {
		$resultsArray[$event["client"]["name"]]++;
		$howmany++;
	}
echo "<br><h1>Dernulf, de e $howmany stycken</h1>";
	foreach ($resultsArray as $key => $value) {
		echo "$key: $value<br>";
	}
	
?>
<html><body>

</body>
</html>