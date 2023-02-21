#!/usr/bin/env php

<?php

include_once "includes/connection.php";
include_once "includes/player_aliases.php";

// DB tables
$tables =  array(
	"SourceTV_Survival_Main",
	"SourceTV_Survival_LoggedEvents",
	"SourceTV_Survival_ConnectionLog",
	"SourceTV_Survival_PlayerClips",
	"OnLogAction_Logs"
);

$logFile = new buildLogFile;

foreach ($tables as $table)
{
	UpdatePlayerNamesInDB($table);
}

function UpdatePlayerNamesInDB($table)
{
	global $mysqli, $logFile, $player_aliases, $steamid_ignore;
	
	$iTotal = count($player_aliases);
	$sql_multi_query = "";
	$query_limit = 50;
	
	foreach ($player_aliases as $authID => $name)
	{
		// skip deleted steam accounts
		if (in_array($authID, $steamid_ignore))
		{
			continue;
		}
		
		$name = $mysqli->real_escape_string($name);
		
		if ($table === "SourceTV_Survival_Main")
		{
			for ($i = 1; $i <= 4; $i++)
			{
				$query = "UPDATE `$table` SET p{$i}_name = \"$name\" WHERE p{$i}_authID = \"$authID\";";
				$sql_multi_query .= $query . "\n";
			}
		}
		
		elseif ($table === "SourceTV_Survival_LoggedEvents" 
		||		$table === "SourceTV_Survival_ConnectionLog"
		||		$table === "SourceTV_Survival_PlayerClips")
		{
			$query = "UPDATE `$table` SET name = \"$name\" WHERE authID = \"$authID\";";
			$sql_multi_query .= $query . "\n";
		}
		
		elseif ($table === "OnLogAction_Logs")
		{
			$query = "UPDATE `$table` SET client_name = \"$name\" WHERE client_authID = \"$authID\";";
			$sql_multi_query .= $query . "\n";
			
			$query = "UPDATE `$table` SET target_name = \"$name\" WHERE target_authID = \"$authID\";";
			$sql_multi_query .= $query . "\n";
		}
		
		else
		{
			$logFile -> LogFatalError("Table '$table' not yet defined in UpdatePlayerNamesInDB()");
		}
		
		// flush so the query doesn't get too big
		if (substr_count( $sql_multi_query, "\n" ) > $query_limit)
		{
			$mysqli->multi_query($sql_multi_query);
			$sql_multi_query = "";
		}
	}
	
	if (!empty($sql_multi_query))
	{
		$mysqli->multi_query($sql_multi_query);
	}
}