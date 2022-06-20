#!/usr/bin/env php
<?php

include_once "includes/connection.php";
include_once "includes/player_aliases.php";
include_once "includes/helpers.php";
include_once "includes/DB_helpers.php";

// DB tables
$tables =  array(
	"SourceTV_Survival_Main",
	"SourceTV_Survival_LoggedEvents",
	"SourceTV_Survival_ConnectionLog",
	"SourceTV_Survival_PlayerClips",
	"OnLogAction_Logs"
);

$logFile = new buildLogFile;
$g_aPlayerList = [];

$g_aCheckedSteamAccounts = []; // cleared each table iteration
$g_aMissingPlayers = array(); // not cleared

/* Correct each alias within the DB tables */
foreach ($tables as $table)
{
	UpdatePlayerNamesInDB($table); // correct names in DB table
	FindMissingAuthIDs($table); // find names not yet defined in player_aliases.php
	
	// reset these each iteration
	$g_aCheckedSteamAccounts = [];
	$g_iRowCount = 0;
	
	if ($table === "SourceTV_Survival_Main")
	{
		for ($i = 1; $i <= 4; $i++)
		{
			GeneratePlayersArray($table, "p{$i}_name", "p{$i}_authID");
		}
	}
	elseif ($table === "SourceTV_Survival_LoggedEvents")
	{
		GeneratePlayersArray($table, "name", "authID");
	}
	elseif ($table === "OnLogAction_Logs")
	{
		GeneratePlayersArray($table, "client_name", "client_authID");
	}
	/*
	   Skipping player clips and connection log tables
	*/
	elseif ($table === "SourceTV_Survival_ConnectionLog" || $table === "SourceTV_Survival_PlayerClips")
	{
		continue;
	}
	else
	{
		$logFile -> LogFatalError("Table '$table' not yet defined in main.php foreach() loop.");
	}
}

/* generate player json files*/
GeneratePlayersJsonFile();

/* generate MissingPlayers.md file */

// first sort missing steam IDs by # of rounds logged to the DB
array_multisort( array_column($g_aMissingPlayers, "count"), SORT_DESC, $g_aMissingPlayers );

$missingPlayersFile = getcwd() . "/logs/MissingPlayers.md";
$fp = fopen($missingPlayersFile, 'w');
$date = date("F j, Y, g:i a");
fwrite($fp, "\n-------- $date --------  \n");
fwrite($fp, "Consider adding the following steam64IDs to includes/player_aliases.php:  \n");

fwrite($fp, "\n| SteamID           | Rounds logged in SourceTV DB | Screenshots (verification) |  ");
fwrite($fp, "\n|-------------------|------------------------------|------------------------------|  ");

foreach ($g_aMissingPlayers as $index => $key)
{
	$auth64 = $key['ID'];
	$count = $key['count'];
	
	fwrite($fp, "\n| [$auth64](https://steamcommunity.com/profiles/$auth64) | $count                        | [link](https://steamcommunity.com/profiles/$auth64/screenshots/?appid=550&sort=newestfirst&browsefilter=myfiles&view=imagewall) |  ");
}

fclose($fp);

$logFile->GenerateLogFile();
$logFile->GenerateDebugOutputFile();