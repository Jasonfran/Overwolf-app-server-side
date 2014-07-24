<?php 
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
	require 'vendor/autoload.php';

	use LeagueWrap\Api;

	$opts = array(
		'http'=>array(
			'method'=>"GET",
			'header'=>"X-Mashape-Authorization: hqBjBrX0GqmshBI198jjuc4rbLE1p19B59YjsnQxrWUxsV8iMi"
		)
	);

	$context = stream_context_create($opts);

	// Open the file using the HTTP headers set above
	$file = file_get_contents('https://community-league-of-legends.p.mashape.com/api/v1.0/na/summoner/retrieveInProgressSpectatorGameInfo/meatmash', false, $context);

	$JSONdecoded = json_decode($file);
	if(!$JSONdecoded->success == "false"){
		$teamOne = $JSONdecoded->game->teamOne->array;
		$teamTwo = $JSONdecoded->game->teamTwo->array;
		$success = [
			"success" => "true"
			];
		$teamOneArray = [];
		for ($i=0; $i < count($teamOne); $i++) { 
			$teamOneArray["teamone"][$i] = $teamOne[$i]->summonerName;
		}

		$teamTwoArray = [];
		for ($i=0; $i < count($teamOne); $i++) { 
			$teamOneArray["teamtwo"][$i] = $teamTwo[$i]->summonerName;
		}
		
		/*NEED TO GET SUMMONER SPELLS NEXT, BECAUSE MY WAY WASNT WORKING*/

		$summonerSpells = $JSONdecoded->game->playerChampionSelections->array;
		
		$summonerSpellsArray = [];

		for ($i=0; $i < count($summonerSpells); $i++) { 
			$summonerSpellsArray['summonerSpells'][$summonerSpells[$i]->summonerInternalName] = $summonerSpells[$i]->spell1Id." ".$summonerSpells[$i]->spell2Id;
		}

		$bothTeams = array_merge(array_merge($success, $teamOneArray), $teamTwoArray);
		$bothTeamsAndSpells = array_merge($bothTeams, $summonerSpellsArray);
		echo(json_encode($bothTeamsAndSpells, JSON_PRETTY_PRINT));

		
	}
	else{
		// echo('{
		// 	"message":"Player not in a game"
		// }');
		echo($file);
	}
 ?>