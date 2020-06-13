<?php
	require_once('config.php');
	require_once('apikey.php');
	$website = "https://opendata.cwb.gov.tw/fileapi/v1/opendataapi/{$dataid}?Authorization={$apikey}&format=JSON";
	
	$getweather = file_get_contents($website);
	$weatherarray = json_decode($getweather,TRUE);
	
	$msgsjson = $weatherarray["cwbopendata"]["dataset"]["parameterSet"]["parameter"];
	$msgs = array();
	foreach($msgsjson as $msg) {

		array_push($msgs, $msg["parameterValue"]);	//FIFO
	}
	
	//Send only one
	$smsg = urlencode($msgs[2]);
	$website = "https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&text={$smsg}";
	$update = file_get_contents($website);
	$updatearray = json_decode($update, TRUE);
	
	if ((bool)$updatearray["ok"]){
		echo '[  OK  ]<br><br>';
	} else{
		echo '[ Fail ]<br><br>';
	}
	
?>