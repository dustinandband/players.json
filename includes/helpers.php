<?php
include_once "player_aliases.php";

class buildLogFile
{
	private $log_general;
	private $log_errors;
	private $log_debugOutput;
	private $eventsLogged;
	private $errorsLogged;
	private $debugLogged;
	
	function __construct() {
		$this->log_general = "  Logged events:\n";
		$this->log_errors = "  Errors:\n";
		$this->log_debugOutput = "  Debug output:\n\n";
		$this->eventsLogged = $this->errorsLogged = $this->debugLogged = False;
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
	
	function LogDebugOutput($message) {
		$this->log_debugOutput .= $message . "\n";
		if (!$this->debugLogged) {
			$this->debugLogged = True;
		}
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
	
	function GenerateDebugOutputFile() {
		// don't bother if no events got logged.
		if (!$this->debugLogged) {
			return;
		}
		
		$fp = fopen(DEBUG_LOGFILE, 'w');
		$date = date("F j, Y, g:i a");
		fwrite($fp, "\n-------- $date --------\n\n");
		fwrite($fp, $this->log_debugOutput . "\n");
		fclose($fp);
	}
}

function GetPlayerName($authID)
{
	global $logFile;
	
	$file = @file_get_contents(sprintf("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=%s", constant("STEAM_WEB_API"), $authID));
	
	if (!$file)
	{
		return "";
	}
	
	$json2 = json_decode($file);
	if (!key_exists("0", $json2->response->players))
	{
		return "";
	}
	return $json2->response->players[0]->personaname;
}

function GetProfileImage($authID)
{
	global $logFile;
	
	$file = @file_get_contents(sprintf("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=%s&steamids=%s", constant("STEAM_WEB_API"), $authID));
	
	if (!$file)
	{
		return "";
	}
	
	$json2 = json_decode($file);
	if (!key_exists("0", $json2->response->players))
	{
		return "";
	}
	return $json2->response->players[0]->avatarfull;
}

function GeneratePlayersArray($table, $row_name, $row_authID)
{
	global $mysqli, $logFile, $g_aCheckedSteamAccounts, $g_aPlayerList, $g_iRowCount, $player_aliases, $steamid_ignore;
	
	$query = "SELECT $row_name, $row_authID FROM `$table` ORDER BY $row_name;";
	
	if (!$result = $mysqli->query($query))
	{
		$logFile->LogFatalError("MySQL error:\n    $mysqli->error \n    query: \n    $query");
	}
	
	$Error_Count = 0;
	while($row = $result->fetch_assoc())
	{
		// assume steam is down if we can't get player names or images for this many clients..
		if ($Error_Count > 40)
		{
			$logFile->LogFatalError("helpers.php : GeneratePlayersArray() : Steam is probably down. Aborting... ");
		}

		$AuthID = $row[$row_authID];
		// ignore if not human player
		if ($AuthID === 'BOT' || $AuthID === '' || in_array($AuthID, $steamid_ignore))
		{
			continue;
		}
		
		// skip steamIDs we already checked
		if (in_array($AuthID, $g_aCheckedSteamAccounts))
		{
			continue;
		}
		array_push($g_aCheckedSteamAccounts, $AuthID);
		
		// Name wouldn't have gotten corrected in DB if not defined.
		// Just grab current alias used by player
		if (!array_key_exists($AuthID, $player_aliases))
		{
			$name = htmlspecialchars(GetPlayerName($AuthID), ENT_QUOTES, 'UTF-8');
		}
		else
		{
			$name = htmlspecialchars($row[$row_name], ENT_QUOTES, 'UTF-8');
		}

		if ( $name === "")
		{
			$logFile->LogEvent("Error retrieving player name for [$AuthID]. skipping..");
			$Error_Count++;
			continue;
		}
		
		// names with backslashes cause the players listing to error out
		// since it exists within <script> tags.
		$name = str_replace('\\','\\\\', $name);
		
		$avatar = GetProfileImage($AuthID);
		if ( $avatar === "")
		{
			$logFile->LogEvent("Error retrieving player image for $name [$AuthID]. skipping..");
			$Error_Count++;
			continue;
		}
		
		$g_aPlayerList["$table"][$g_iRowCount] = array(
			"name" => $name,
			"Steam64ID" => $AuthID,
			"avatar"=>$avatar,
			"CommunityURL" => "https://steamcommunity.com/profiles/" . $AuthID . "/"
		);
		$g_iRowCount++;
	}
}

function GeneratePlayersJsonFile()
{
	global $g_aPlayerList, $logFile;
	
	$keys = array_keys($g_aPlayerList);
	
	// SourceTV_Survival_Main , SourceTV_Survival_LoggedEvents, ..
	foreach($keys as $table)
	{
		$fp = fopen("PlayerList/$table.json", "w");
		fwrite($fp, "[{");
		$FirstHumanClient = True;
		
		$subkeys = array_keys($g_aPlayerList["$table"]);
		
		foreach($subkeys as $authIDKey)
		{
			$playerinfo = array_values($g_aPlayerList["$table"]["$authIDKey"]);
			$name = $playerinfo[0];
			$AuthID = $playerinfo[1];
			$avatar = $playerinfo[2];
			$CommunityURL = $playerinfo[3];
			
			if ($FirstHumanClient)
			{
				fwrite($fp, "\t\"name\":\"" . $name . "\",\n");
				$FirstHumanClient = False;
			}
			else
			{
				fwrite($fp, ",\n{\n\t\"name\":\"" . $name . "\",\n");
			}
			
			fwrite($fp, "\t\"Steam64ID\":\"" . $AuthID . "\",\n");
			fwrite($fp, "\t\"picture\":\"" . $avatar . "\",\n");
			fwrite($fp, "\t\"CommunityURL\":\"" . $CommunityURL. "\"\n");
			fwrite($fp, "}");
		}
		fwrite($fp, "]");
		fclose($fp);
	}
}

function in_missing_players_array($AuthID, $array)
{
	foreach ($array as $index => $key)
	{
		if (in_array($AuthID, $key))
		{
			return true;
		}
	}
	return false;
}