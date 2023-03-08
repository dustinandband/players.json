#!/usr/bin/env php

<?php

include_once "includes/autoloader.php";

main();

function main()
{
	FindMissingAuthIDs();
	logFile::GenerateMissingPlayersFile();
	
	// So we don't have to iterate through the DB again while getting steam info for names / avatars for dropdown menus:
	logFile::GenerateSteamIDsArray_jsonFile("steamIDs_everyone", "SourceTV_Survival_Main.json");
	logFile::GenerateSteamIDsArray_jsonFile("steamIDs_SourceTV_Survival_LoggedEvents", "SourceTV_Survival_LoggedEvents.json");
	logFile::GenerateSteamIDsArray_jsonFile("steamIDs_OnLogAction_Logs", "OnLogAction_Logs.json");
}

function FindMissingAuthIDs()
{
	$mysqli = connection::establishDBConnection();
	
	// 1st query, all relevant steamIDs from rounds played
	$query = <<<queryString
SELECT p1_authID as 'authID' FROM `SourceTV_Survival_Main`
UNION
SELECT p2_authID as 'authID' FROM `SourceTV_Survival_Main`
UNION
SELECT p3_authID as 'authID' FROM `SourceTV_Survival_Main`
UNION
SELECT p4_authID as 'authID' FROM `SourceTV_Survival_Main`
ORDER BY `authID` ASC;
queryString;
	
	if (!$result = $mysqli->query($query))
	{
		$logFile->LogError("MySQL error:\n$mysqli->error \n     query:\n    $query");
		return;
	}
	
	$iter = 0;
	while($row = $result->fetch_assoc())
	{
		$iter++;
		$AuthID = $row['authID'];
		PushToCheckedSteamAccounts($AuthID, $iter, $result->num_rows);
	}
	
	// 2nd query, sourceTV logged events
	$query = "SELECT DISTINCT `authID` FROM `SourceTV_Survival_LoggedEvents`;";
	if (!$result = $mysqli->query($query))
	{
		$logFile->LogError("MySQL error:\n$mysqli->error \n     query:\n    $query");
		return;
	}
	else
	{
		while($row = $result->fetch_assoc())
		{
			$AuthID = $row['authID'];
			if (!is_numeric($AuthID) || in_array($AuthID, data::$steamid_ignore)) { continue;}
			global_vars::PushValue("steamIDs_SourceTV_Survival_LoggedEvents", $AuthID, true);
		}
	}
	
	// 3rd query, general logged events
	$query =<<<queryString
SELECT `client_authID` as 'authID' FROM `OnLogAction_Logs`
UNION
SELECT `target_authID` as 'authID' FROM `OnLogAction_Logs`
ORDER BY `authID` ASC;
queryString;

	if (!$result = $mysqli->query($query))
	{
		$logFile->LogError("MySQL error:\n$mysqli->error \n     query:\n    $query");
		return;
	}
	else
	{
		while($row = $result->fetch_assoc())
		{
			$AuthID = $row['authID'];
			if (!is_numeric($AuthID) || in_array($AuthID, data::$steamid_ignore)) { continue;}
			global_vars::PushValue("steamIDs_OnLogAction_Logs", $AuthID, true);
		}
	}
	
	connection::KillDBConnection($mysqli);
}


function PushToCheckedSteamAccounts($AuthID, $iteration, $total_resultset)
{
	if (!is_numeric($AuthID) || in_array($AuthID, data::$steamid_ignore)) {
		return;
	}
	
	// Not defined in data::$player_aliases
	if (!array_key_exists($AuthID, data::$player_aliases))
	{
		global_vars::AddToMissingPlayersArray($AuthID);
	}
	
	global_vars::PushValue("steamIDs_everyone", $AuthID, true);
	
	$progress = number_format(($iteration / $total_resultset) * 100, 2);
	echo "PushToCheckedSteamAccounts [$AuthID]. Progress: $iteration / $total_resultset ($progress%)" . PHP_EOL;
}