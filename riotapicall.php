<?php
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
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
			"basicStats" => [
				"level" => $summonerInfo->summonerLevel,
				"name" => $summonerInfo->name,
				"profileIcon" => $summonerInfo->profileIconId
			]
		];

		// my summoner id 41217037

	try{	
		$summonerLeague = $api->league()->league($summonerInfo->id, true);
		$summonerLeagueArray = [
		"rankedSummary" =>[
			"leagueName" => $summonerLeague[0]->name,
			"division" => $summonerLeague[0]->tier." ".$summonerLeague[0][0]->division,
			"LP" => $summonerLeague[0][0]->leaguePoints,
			"rankedWins" => $summonerLeague[0][0]->wins
			]
		];
	}catch(Exception $e){

		$summonerLeagueArray = [
		"rankedSummary" => [
			"leagueName" => "Unranked",
			"division" => "Unranked",
			"LP" => "Unranked",
			"rankedWins" => "Unranked"
			]
		];
	}

	$basicAndRanked = array_merge($summonerInfoArray, $summonerLeagueArray);


	try{
		$season = $api->stats()->setSeason("SEASON4");
		$rankedStats = $season->summary($summonerInfo->id)[5];

		//print_r($rankedStats);

		$rankedStatsArray = [
			"rankedStats" => [
				"wins" => $rankedStats->wins,
				"losses" => $rankedStats->losses,
				"totalGames" => ($rankedStats->wins + $rankedStats->losses),
				"Total Champion Kills" => $rankedStats->aggregatedStats->totalChampionKills,
				"Total Minions Killed" => $rankedStats->aggregatedStats->totalMinionKills,
				"Total Turrets Destroyed" => $rankedStats->aggregatedStats->totalTurretsKilled,
				"Total Jungle Creeps Killed" => $rankedStats->aggregatedStats->totalNeutralMinionsKilled,
				"Total Assists" => $rankedStats->aggregatedStats->totalAssists
			]
		];
	}catch(Exception $e){
		$rankedStatsArray = [
			"rankedStats" => [
				"wins" => "Unranked",
				"losses" => "Unranked",
				"totalGames" => "Unranked",
				"totalChampionKills" => "Unranked",
				"totalMinionKills" => "Unranked",
				"totalTurretsKilled" => "Unranked",
				"totalJungleKilled" => "Unranked",
				"totalAssists" => "Unranked"
			]
		];
	}
	echo(json_encode(array_merge($basicAndRanked, $rankedStatsArray), JSON_PRETTY_PRINT));
?>
