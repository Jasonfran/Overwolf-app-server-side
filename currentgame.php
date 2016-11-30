<?php 
	header('Access-Control-Allow-Origin: *');
	header('Content-Type: application/json');
	require 'vendor/autoload.php';

	use LeagueWrap\Api;

	$opts = array(
		'http'=>array(
			'method'=>"GET",
			'header'=>"X-Mashape-Authorization: auth key"
		)
	);

	$context = stream_context_create($opts);

	if (($_GET["region"] || $_GET["summoner"]) == NULL){
		exit("No region or summoner name!");
	}

	$region = $_GET["region"];
	$summonerName = str_replace(' ', '', htmlspecialchars_decode($_GET["summoner"]));

	$regions = ["BR", "EUNE", "EUW", "LAN", "LAS", "NA", "OCE", "RU", "TR", "EU"];

	if ($region == "KR") {
		exit('{"success": "false","error": "Sorry, cant connect to korean API for current game information"}');
	}
	elseif (!in_array($region, $regions)){
		exit("Invalid region!");
	}

	// Open the file using the HTTP headers set above
	$file = file_get_contents('https://community-league-of-legends.p.mashape.com/api/v1.0/'.strtolower($region).'/summoner/retrieveInProgressSpectatorGameInfo/'.$summonerName, false, $context);

	$JSONdecoded = json_decode($file);

	//echo($file);


	if(!$JSONdecoded->success == "false"){
		$teamOne = $JSONdecoded->game->teamOne->array;
		$teamTwo = $JSONdecoded->game->teamTwo->array;
		$myKey = "api key";

		$api = new Api($myKey); // Load up the API
		$api->setRegion($region); 
		$english = $api->staticData()->setLocale('en_US');
		$championData = $english->getChampions("info");
		$latestVersion = $api->staticData()->version()[0];
		$allSummoners = [];
		$success = [
			"success" => "true",
			"version" => $latestVersion
			];

		$summonerSpells = $JSONdecoded->game->playerChampionSelections->array;
		
		$summonerSpellsArray = [];
		
		

		for ($i=0; $i < count($summonerSpells); $i++) { 
			
			$champName = $championData[$summonerSpells[$i]->championId]->name;
			$summonerSpellsArray['summonerSpells'][$summonerSpells[$i]->summonerInternalName] = $summonerSpells[$i]->spell1Id." ".$summonerSpells[$i]->spell2Id." ".$champName;
		}

		$teamOneArray = [];
		for ($i=0; $i < count($teamOne); $i++) { 
			$theName = strtolower(str_replace(' ', '', $teamOne[$i]->summonerName));
			$theirSpellsAndChamp = $summonerSpellsArray['summonerSpells'][$theName];
			$teamOneArray["teamOne"][$teamOne[$i]->summonerName] = $theirSpellsAndChamp;
			array_push($allSummoners, $teamOne[$i]->summonerName);
		}

		$teamTwoArray = [];
		for ($i=0; $i < count($teamTwo); $i++) { 
			$theName = strtolower(str_replace(' ', '', $teamTwo[$i]->summonerName));
			$theirSpellsAndChamp = $summonerSpellsArray['summonerSpells'][$theName];
			$teamOneArray["teamTwo"][$teamTwo[$i]->summonerName] = $theirSpellsAndChamp;
			array_push($allSummoners, $teamTwo[$i]->summonerName);
		}


    	//print_r($allSummoners);

    	

		$summoner = $api->summoner();
		
		$allSummonerInfo = $summoner->info($allSummoners);



		//print_r($allSummonerInfo);

		$summonerInfoArray = [];
		$allSummonerIds = [];
		$allSummonerIdsForStats = [];

		foreach ($allSummonerInfo as $key => $value) { 
		 	$allSummonerIds[$allSummonerInfo[$key]->name." ".$allSummonerInfo[$key]->summonerLevel] = $allSummonerInfo[$key]->id;
		 	array_push($allSummonerIdsForStats,$allSummonerInfo[$key]->id);
		}

		try{
			$league = $api->league()->league($allSummonerIds, true);

			//print_r($league);

			foreach ($allSummonerInfo as $key => $value) {
				$summonerInfoArray['summonerRanks'][$allSummonerInfo[$key]->name] = [
					"id" => $allSummonerInfo[$key]->id,
					"level" => $allSummonerInfo[$key]->summonerLevel,
					"name" => $allSummonerInfo[$key]->name,
					"systemName" => strtolower(str_replace(' ', '', $allSummonerInfo[$key]->name)),

				];
			}

			foreach ($summonerInfoArray['summonerRanks'] as $key => $value) {
				
				$summonerInfoArray['summonerRanks'][$key]['league'] = $league[$summonerInfoArray['summonerRanks'][$key]['id']][0]->tier." ".$league[$summonerInfoArray['summonerRanks'][$key]['id']][0][0]->division;
				$summonerInfoArray['summonerRanks'][$key]['wins'] = $league[$summonerInfoArray['summonerRanks'][$key]['id']][0][0]->wins;
				$summonerInfoArray['summonerRanks'][$key]['lp'] = $league[$summonerInfoArray['summonerRanks'][$key]['id']][0][0]->leaguePoints;
			}
		}catch(Exception $e){
			
		}
		

		$bothTeams = array_merge(array_merge($success, $teamOneArray), $teamTwoArray);
		$bothTeamsWithRanks = array_merge($bothTeams, $summonerInfoArray);
		echo(json_encode($bothTeamsWithRanks, JSON_PRETTY_PRINT));

		
	}
	else{
		// echo('{
		// 	"message":"Player not in a game"
		// }');
		echo($file);
	}
 ?>
