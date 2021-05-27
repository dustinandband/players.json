#!/usr/bin/env php
<?php

include_once "connection.php";
include_once "player_aliases.php";
include_once "helpers.php";


$logFile = new buildLogFile;

if (!UpdatePlayerNamesInDB("SourceTV_Survival_PlayerInfo") || !UpdatePlayerNamesInDB("SourceTV_Survival_LoggedEvents"))
{
	$logFile->GenerateLogFile();
	return;
}

$query = "SELECT * FROM `SourceTV_Survival_PlayerInfo` ORDER BY name;";
if (!$result = $mysqli->query($query))
{
	$logFile->LogFatalError("MySQL error:\n    $mysqli->error \n    query: \n    $query");
}

// no results
if ($result->num_rows == 0)
{
	$logFile->LogEvent("No results returned for query:\n    $query");
	$logFile->GenerateLogFile();
	return;
}

$checkedSteamAccounts = [];

$fp = fopen("players_temp.json", 'w');
fwrite($fp, "[{");

$i = 0;
$FirstHumanClient = True;
while($row = $result->fetch_assoc())
{
	$i++;
	if ($result->num_rows == $i)
	{
		fwrite($fp, "]");
		break;
	}
	
	$AuthID = $row['authID'];
	if ($AuthID === 'BOT' || $AuthID === '')
	{
		continue;
	}
	
	if (in_array($AuthID, $checkedSteamAccounts))
	{
		continue;
	}
	
	array_push($checkedSteamAccounts, $AuthID);
	$name = htmlspecialchars($row['name']);
	
	// names with backslashes cause the players listing to error out
	// since it exists within <script> tags.
	$name = str_replace('\\','\\\\', $name);
	
	$avatar = GetProfileImage($AuthID);
	if ( $avatar === "")
	{
		$logFile->LogEvent("Error retrieving player image for $name [$AuthID]. skipping..");
		continue;
	}
	
	// in case bot is displayed as first result
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
	fwrite($fp, "\t\"CommunityURL\":\"https://steamcommunity.com/profiles/" . $AuthID . "/\"\n");
	fwrite($fp, "}");
}

fclose($fp);
$logFile->GenerateLogFile();
rename("players_temp.json", "players.json");