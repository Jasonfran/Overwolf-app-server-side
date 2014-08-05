<?php

	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
	
	$status = file_get_contents("https://dl.dropboxusercontent.com/u/53506152/Tools%20for%20the%20rift%20status/status.txt");

	echo($status);

?>