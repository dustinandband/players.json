#!/usr/bin/env php

<?php

include_once "includes/autoloader.php";


define("API_LIMIT", 99);
define("STEAMIDS_DIR", "logs/steamIDs");

main();

function main()
{
	if (!$files = scandir(STEAMIDS_DIR))
	{
		$logFile = new logFile();
		$logFile->LogFatalError("Main() No such directory: " . STEAMIDS_DIR);
		
	}
	
	foreach($files as $file)
	{
		// ignore non-files
		if (strpos($file, ".json") === false) { continue; }
		
		GenerateJsonFile($file);
	}
}

function GenerateJsonFile($file)
{
	$logFile = new logFile();
	
	if (!$contents = file_get_contents(STEAMIDS_DIR . "/$file"))
	{
		$logFile->LogError("GenerateJsonFile: No such file: " . STEAMIDS_DIR . "/$file");
		return;
	}
	$steamIDs = json_decode($contents, true);
	
	$url_string = resetURLString();
	$PlayersInfo = [];
	
	$numSteamIDs = count($steamIDs);
	$iter_resetable = $iter = 0;
	
	foreach($steamIDs as $steamID)
	{
		$url_string .= sprintf("%s,", $steamID);
		
		// Make API call, reset $iter_resetable & $url_string
		if (++$iter_resetable === API_LIMIT || ++$iter === $numSteamIDs)
		{
			$url_string = rtrim($url_string, ',');
			
			if (!is_null($playerinfo_json = MakeAPICall($url_string)))
			{
				$playerinfo_json = $playerinfo_json->response->players;
				foreach($playerinfo_json as $item)
				{
					$steam64ID = $item->steamid;
					if (array_key_exists($steam64ID, data::$player_aliases))
					{
						$name = data::$player_aliases[$steam64ID];
					}
					else
					{						
						$name = $item->personaname;
					}
					// Make sure each name can safely exist in <script> tags
					$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
					$name = str_replace('\\','\\\\', $name);
					$temp_buffer = array(
						"name" => $name,
						"Steam64ID" => $steam64ID,
						"picture" => $item->avatarfull,
					);
					array_push($PlayersInfo, $temp_buffer);
				}
			}
			else
			{
				// TODO log function call that caused error
				$errormsg = sprintf("Call to steam API returned null. Steam probably down...");
				$logFile->LogFatalError($errormsg);
			}
			
			$url_string = resetURLString();
			$iter_resetable = 0;
			if ($numSteamIDs === $iter) {break;}
		}
	}
	
	// Sort so names appear alphabetically in drop-down menu
	array_multisort( array_column($PlayersInfo, "name"), SORT_NATURAL | SORT_FLAG_CASE, $PlayersInfo );
	
	$dirname = getcwd() . "/PlayerList/";
	if (!is_dir($dirname))
	{
		echo "Creating directory: $dirname" . PHP_EOL;
		mkdir($dirname, 0755, true);
	}
	$fileName = $dirname . $file;
	
	$fp = fopen($fileName, 'w');
	fwrite($fp, print_r(json_encode($PlayersInfo), true));
	fclose($fp);
	
	$logFile->GenerateLogFile();
}

function resetURLString(): string
{
	return sprintf("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=", config::getValue("STEAM_WEB_API"));
}

function MakeAPICall($url_string)
{
	$file = @file_get_contents($url_string);
	if (!$file) {return null;}
	return json_decode($file);
}