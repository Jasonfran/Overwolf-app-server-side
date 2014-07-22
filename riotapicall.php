<?php
	header('Access-Control-Allow-Origin: *');
	require 'vendor/autoload.php';

	use LeagueWrap\Api;

	$myKey = "f3eea3f7-c790-4f52-bef0-3fe0f98e9392";

	if (($_GET["region"] || $_GET["summoner"]) == NULL){
		exit("No region or summoner name!");
	}

	$region = $_GET["region"];
	$summonerName = $_GET["summoner"];

	$regions = ["BR", "EUNE", "EUW", "KR", "LAN", "LAS", "NA", "OCE", "RU", "TR"];

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

		// my summoner id 41217037

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

	$season = $api->stats()->setSeason("SEASON4");
	$rankedStats = $season->summary($summonerInfo->id)[5];

	//print_r($rankedStats);

	$rankedStatsArray = [
		"rankedStats" => [
			"wins" => $rankedStats->wins,
			"losses" => $rankedStats->losses,
			"totalGames" => ($rankedStats->wins + $rankedStats->losses),
			"totalChampionKills" => $rankedStats->aggregatedStats->totalChampionKills,
			"totalMinionKills" => $rankedStats->aggregatedStats->totalMinionKills,
			"totalTurretsKilled" => $rankedStats->aggregatedStats->totalTurretsKilled,
			"totalJungleKilled" => $rankedStats->aggregatedStats->totalNeutralMinionsKilled,
			"totalAssists" => $rankedStats->aggregatedStats->totalAssists
		]
	];
	echo(json_encode($rankedStatsArray));
?>
