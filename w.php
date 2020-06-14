<?php
	require_once('config.php');
	require_once('apikey.php');
	require_once('tg_key.php');
	$website = "https://opendata.cwb.gov.tw/fileapi/v1/opendataapi/{$dataid}?Authorization={$apikey}&format=JSON";
	
	$getweather = file_get_contents($website);
	$weatherarray = json_decode($getweather,TRUE);
	
	$msgsjson = $weatherarray["cwbopendata"]["dataset"]["parameterSet"]["parameter"];
	$issueTime = $weatherarray["cwbopendata"]["dataset"]["datasetInfo"]["issueTime"];
	//var_dump($issueTime);
	$ts_file = fopen("timestamp.txt", "r") or die("Unable to open file!");
	$timestamp = fgets($ts_file);
	fclose($ts_file);
	//var_dump(substr_compare($timestamp, $issueTime, 0));
	if (substr_compare($timestamp, $issueTime, 0) != 0){
		
		$msgs = array();
		foreach($msgsjson as $msg) {
			array_push($msgs, $msg["parameterValue"]);	//FIFO
		}
	
		//Send only one
		$smsg = urlencode($msgs[2]);
		$website = "https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&text={$smsg}";
		$update = file_get_contents($website);
		$updatearray = json_decode($update, TRUE);
		/*
		if ((bool)$updatearray["ok"]){
			echo '[  OK  ]<br><br>';
		} else{
			echo '[ Fail ]<br><br>';
		}
		*/
		//Overwrite timestamp
		$ts_file = fopen("timestamp.txt", "w+") or die("Unable to write file!");
		fwrite($ts_file, $issueTime);
		fclose($ts_file);
	}
?>