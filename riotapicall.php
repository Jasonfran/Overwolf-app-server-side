<?php
	require 'vendor/autoload.php';

	use LeagueWrap\Api;

	$myKey = "f3eea3f7-c790-4f52-bef0-3fe0f98e9392";

	if (($_GET["region"] || $_GET["summoner"]) == NULL){
		exit("No region or summoner name!");
	}

	$region = $_GET["region"];
	$summonerName = $_GET["summoner"];

	$regions = ["br", "eune", "euw", "kr", "lan", "las", "na", "oce", "ru", "tr"];

	if (!in_array($region, $regions)){
		exit("Invalid region!");
	}

	$api = new Api($myKey); // Load up the API
	$api->setRegion($region);          
	$summonerInfo = $api->summoner()->info($summonerName);

	$summonerInfoArray = [
			"level" => $summonerInfo->summonerLevel,
			"name" => $summonerInfo->name,
			"profileIcon" => $summonerInfo->profileIconId

		];

	try{	
		$summonerLeague = $api->league()->league($summonerInfo->id, true);

		$summonerLeagueArray = [
			"leagueName" => $summonerLeague[0]->name,
			"division" => $summonerLeague[0]->tier." ".$summonerLeague[0][0]->division,
			"LP" => $summonerLeague[0][0]->leaguePoints,
			"rankedWins" => $summonerLeague[0][0]->wins
		];
	}catch(Exception $e){

		$summonerLeagueArray = [
			"leagueName" => "null",
			"division" => "null",
			"LP" => "null",
			"rankedWins" => "null"
		];
	}

	echo(json_encode(array_merge($summonerInfoArray, $summonerLeagueArray)));
?>
