<?php
include_once "connection.php";
include_once "player_aliases.php";

class buildLogFile
{
	private $log_general;
	private $log_errors;
	private $eventsLogged;
	private $errorsLogged;
	
	function __construct() {
		$this->log_general = "  Logged events:\n";
		$this->log_errors = "  Errors:\n";
		$this->eventsLogged = $this->errorsLogged = False;
	}
	
	function LogEvent($message) {
		echo $message  . PHP_EOL;
		$this->log_general .= "    " . $message . "\n";
		if (!$this->eventsLogged) {
			$this->eventsLogged = True;
		}
	}
	
	function LogError($errorMsg) {
		echo $errorMsg  . PHP_EOL;
		$this->log_errors .= "    " . $errorMsg . "\n";
		if (!$this->errorsLogged) {
			$this->errorsLogged = True;
		}
	}
	
	function LogFatalError($errorMsg) {
		$this->LogError($errorMsg);
		$this->GenerateLogFile();
		die();
	}
	
	function GenerateLogFile() {
		// don't bother if no events got logged.
		if (!$this->eventsLogged && !$this->errorsLogged) {
			return;
		}
		
		$fp = fopen(LOGNAME, 'a');
		$date = date("F j, Y, g:i a");
		fwrite($fp, "\n-------- $date --------\n\n");
		
		if ($this->eventsLogged) {
			fwrite($fp, $this->log_general . "\n");
		}
		if ($this->errorsLogged) {
			fwrite($fp, $this->log_errors . "\n");
		}
		
		fclose($fp);
	}
}

function GetProfileImage($authID)
{
	global $logFile;
	
	$file = file_get_contents(sprintf("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=%s", constant("STEAM_WEB_API"), $authID));
	
	if (!$file)
	{
		$logFile->LogFatalError("helpers.php : GetProfileImage() : Check 'STEAM_WEB_API' define. Aborting");
	}
	
	$json2 = json_decode($file);
	if (!key_exists("0", $json2->response->players))
	{
		return "";
	}
	return $json2->response->players[0]->avatarfull;
}

function UpdatePlayerNamesInDB($table)
{
	global $mysqli, $logFile;
	$query = "SELECT * FROM `$table`;";
	if (!$result = $mysqli->query($query))
	{
		$logFile->LogError("helpers.php : UpdatePlayerNamesInDB() : MySQL error:\n$mysqli->error \n     query:\n    $query");
		return false;
	}
	
	if ($result->num_rows == 0)
	{
		$logFile->LogEvent("helpers.php: No results returned for query:\n    $query");
		return false;
	}
	
	global $player_aliases;
	
	$EntriesUpdated = "";
	$PlayersNotFound = "";
	$checkedSteamAccounts = [];
	
	while($row = $result->fetch_assoc())
	{
		$DC_authID = False;
		
		$AuthID = $row['authID'];
		if ($AuthID === 'BOT' && $table === "SourceTV_Survival_PlayerInfo")
		{
			if ($row['dc_prevAuthID'] === "none")
			{
				continue;
			}
			$AuthID = $row['dc_prevAuthID'];
			$DC_authID = True;
		}
		
		// Player is defined in player_aliases
		if (array_key_exists($AuthID, $player_aliases))
		{
			
			// hacky way to get around issue of not being
			// able to use variable names as array keys...
			foreach ($player_aliases as $key => $value)
			{
				if ($key == $AuthID)
				{
					$player_name = $value;
				}
			}
			
			if ($player_name == "")
			{
				continue;
			}
			
			$id = $row['id'];
			$name_escaped = $mysqli->real_escape_string($player_name);
			
			// updating DC name
			if ($DC_authID)
			{
				if ($row['dc_prevName'] !== "none" && $row['dc_prevName'] !== $player_name)
				{
					$UpdateQuery = "UPDATE `$table` SET dc_prevName = '$name_escaped' where id = '$id';";
				}
				else
				{
					continue;
				}
			}
			
			// updating name
			else
			{
				if ($row['name'] === $player_name)
				{
					continue;
				}
				$UpdateQuery = "UPDATE `$table` SET name = '$name_escaped' where id = '$id';";
			}

			if (!$mysqli->query($UpdateQuery))
			{
				$logFile->LogError("helpers.php : UpdatePlayerNamesInDB() : MySQL error:\n$mysqli->error \n    query:\n    $UpdateQuery");
				return false;
			}

			$EntriesUpdated .= "\t\t" . ($DC_authID ? $row['dc_prevName'] : $row['name']) . " changed to $player_name (row $id) " . ($DC_authID ? "(dc_prevName)" : "") . "\n";
		}
		else
		{
			if (!in_array($AuthID, $checkedSteamAccounts))
			{
				$PlayersNotFound .= "\n[$AuthID](https://steamcommunity.com/profiles/$AuthID)  ";
				array_push($checkedSteamAccounts, $AuthID); 
			}
		}
	}
	
	if (sizeof($checkedSteamAccounts) > 0)
	{
		$fp = fopen("MissingPlayers_$table.md", 'w');
		$date = date("F j, Y, g:i a");
		fwrite($fp, "\n-------- $date --------  \n");
		fwrite($fp, "Consider adding the following steam64IDs to player_aliases.php:  \n" . $PlayersNotFound);
		fclose($fp);
	}
	
	if (strlen($EntriesUpdated) > 0)
	{

		$logFile->LogEvent("The following player aliases have been updated, table: '$table':\n" . $EntriesUpdated);
	}
	
	return true;
}

/*
	This is for a different table associated with a diff project
*/
function UpdatePlayerNamesInDB_ActivityLog($table)
{
	global $mysqli2, $logFile, $player_aliases;
	$query = "SELECT id, client_name, client_authID, target_name, target_authID FROM `$table`;";
	
	if (!$result = $mysqli2->query($query))
	{
		$logFile->LogError("helpers.php : UpdatePlayerNamesInDB_ActivityLog() : MySQL error:\n$mysqli2->error \n     query:\n    $query");
		return false;
	}
	
	if ($result->num_rows == 0)
	{
		$logFile->LogEvent("helpers.php: No results returned for query:\n    $query");
		return false;
	}
	
	$EntriesUpdated = "";
	$PlayersNotFound = "";
	$checkedSteamAccounts = [];
	
	while($row = $result->fetch_assoc())
	{
		for ($i = 0; $i <= 1; $i++)
		{
			$AuthID = ($i == 0 ?  $row['client_authID'] : $row['target_authID']);
			if ($AuthID === '' | $AuthID === 'BOT')
			{
				continue;
			}
			
			$player_name = "";
			if (array_key_exists($AuthID, $player_aliases))
			{
				foreach ($player_aliases as $key => $value)
				{
					if ($key == $AuthID)
					{
						$player_name = $value;
					}
				}
			}
			else
			{
				if (!in_array($AuthID, $checkedSteamAccounts))
				{
					$PlayersNotFound .= "\n[$AuthID](https://steamcommunity.com/profiles/$AuthID)  ";
					array_push($checkedSteamAccounts, $AuthID); 
				}
			}
			
			if ($player_name == "")
			{
				continue;
			}
			
			$id = $row['id'];
			$name_escaped = $mysqli2->real_escape_string($player_name);
			
			$name = ($i == 0 ? $row['client_name'] : $row['target_name']);
			if ($name === $player_name)
			{
				continue;
			}
			
			$name_column = ($i == 0 ? "client_name" : "target_name");
			$UpdateQuery = "UPDATE `$table` SET $name_column = '$name_escaped' where id = '$id';";
			
			if (!$mysqli2->query($UpdateQuery))
			{
				$logFile->LogError("helpers.php : UpdatePlayerNamesInDB_ActivityLog() : MySQL error:\n$mysqli2->error \n    query:\n    $UpdateQuery");
				return false;
			}
			
			$EntriesUpdated .= "\t\t" . $name . " changed to $player_name (row $id, column " . ($i == 0 ? "'client_name'" : "'target_name'") . ")\n";
		}
	}
	
	if (sizeof($checkedSteamAccounts) > 0)
	{
		$fp = fopen("MissingPlayers_$table.md", 'w');
		$date = date("F j, Y, g:i a");
		fwrite($fp, "\n-------- $date --------  \n");
		fwrite($fp, "Consider adding the following steam64IDs to player_aliases.php:  \n" . $PlayersNotFound);
		fclose($fp);
	}
	
	if (strlen($EntriesUpdated) > 0)
	{

		$logFile->LogEvent("The following player aliases have been updated, table: '$table':\n" . $EntriesUpdated);
	}
	return true;
}