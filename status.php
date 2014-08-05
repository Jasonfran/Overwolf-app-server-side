<?php

	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
	
	$status = file_get_contents("status.txt");

	echo($status);

?>