<?php
include_once "connection.php";
include_once "player_aliases.php";

function UpdatePlayerNamesInDB($table)
{
	global $mysqli, $logFile, $player_aliases;
	
	foreach ($player_aliases as $authID => $name)
	{
		$name = $mysqli->real_escape_string($name);
		
		// SourceTV_Survival_Main
		if ($table === "SourceTV_Survival_Main")
		{
			for ($i = 1; $i <= 4; $i++)
			{
				$query = "UPDATE `$table` SET p{$i}_name = \"$name\" WHERE p{$i}_authID = \"$authID\";";
				$logFile->LogDebugOutput($query);
				
				if (!$result = $mysqli->query($query))
				{
					$logFile->LogError("helpers.php : UpdatePlayerNamesInDB() : MySQL error:\n$mysqli->error \n     query:\n    $query");
				}
			}
		}
		
		// SourceTV_Survival_LoggedEvents, SourceTV_Survival_ConnectionLog, SourceTV_Survival_PlayerClips
		elseif ($table === "SourceTV_Survival_LoggedEvents" 
		||		$table === "SourceTV_Survival_ConnectionLog"
		||		$table === "SourceTV_Survival_PlayerClips")
		{
			$query = "UPDATE `$table` SET name = \"$name\" WHERE authID = \"$authID\";";
			$logFile->LogDebugOutput($query);
			
			if (!$result = $mysqli->query($query))
			{
				$logFile->LogError("helpers.php : UpdatePlayerNamesInDB() : MySQL error:\n$mysqli->error \n     query:\n    $query");
			}
		}
		
		// OnLogAction_Logs
		elseif ($table === "OnLogAction_Logs")
		{
			$query = "UPDATE `$table` SET client_name = \"$name\" WHERE client_authID = \"$authID\";";
			$logFile->LogDebugOutput($query);
			
			if (!$result = $mysqli->query($query))
			{
				$logFile->LogError("helpers.php : UpdatePlayerNamesInDB() : MySQL error:\n$mysqli->error \n     query:\n    $query");
			}
			
			$query = "UPDATE `$table` SET target_name = \"$name\" WHERE target_authID = \"$authID\";";
			$logFile->LogDebugOutput($query);
			
			if (!$result = $mysqli->query($query))
			{
				$logFile->LogError("helpers.php : UpdatePlayerNamesInDB() : MySQL error:\n$mysqli->error \n     query:\n    $query");
			}
		}
		
		else
		{
			$logFile -> LogFatalError("Table '$table' not yet defined in UpdatePlayerNamesInDB()");
		}
	}
}

function FindMissingAuthIDs($table)
{
	global $mysqli, $player_aliases, $g_sPlayersNotFound;
	
	if ($table === "SourceTV_Survival_Main")
	{
		for ($i = 1; $i <= 4; $i++)
		{
			$offset = "p{$i}_authID";
			$query = "select $offset from `$table`;";
			if (!$result = $mysqli->query($query))
			{
				$logFile->LogError("helpers.php : FindMissingAuthIDs() : MySQL error:\n$mysqli->error \n     query:\n    $query");
				return;
			}
			
			while($row = $result->fetch_assoc())
			{
				$AuthID = $row[$offset];
				PushToCheckedSteamAccounts($AuthID);
			}
		}
	}
	
	// SourceTV_Survival_LoggedEvents, SourceTV_Survival_ConnectionLog, SourceTV_Survival_PlayerClips
	elseif ($table === "SourceTV_Survival_LoggedEvents" 
	||		$table === "SourceTV_Survival_ConnectionLog"
	||		$table === "SourceTV_Survival_PlayerClips")
	{
		$query = "select authID from `$table`;";
		if (!$result = $mysqli->query($query))
		{
			$logFile->LogError("helpers.php : FindMissingAuthIDs() : MySQL error:\n$mysqli->error \n     query:\n    $query");
			return;
		}
		
		while($row = $result->fetch_assoc())
		{
			$AuthID = $row['authID'];
			PushToCheckedSteamAccounts($AuthID);
		}
	}
	
	// OnLogAction_Logs
	elseif ($table === "OnLogAction_Logs")
	{
		$query = "select client_authID, target_authID from `$table`;";
		if (!$result = $mysqli->query($query))
		{
			$logFile->LogError("helpers.php : FindMissingAuthIDs() : MySQL error:\n$mysqli->error \n     query:\n    $query");
			return;
		}
		
		while($row = $result->fetch_assoc())
		{
			$AuthID = $row['client_authID'];
			PushToCheckedSteamAccounts($AuthID);
			$AuthID = $row['target_authID'];
			PushToCheckedSteamAccounts($AuthID);
		}
	}
	
	else
	{
		$logFile -> LogFatalError("Table '$table' not yet defined in FindMissingAuthIDs()");
	}
}

function PushToCheckedSteamAccounts($AuthID)
{
	global $player_aliases, $g_aMissingPlayers, $g_sPlayersNotFound;
	
	if ($AuthID === "")
	{
		return;
	}
	
	if (!array_key_exists($AuthID, $player_aliases))
	{
		if (!in_array($AuthID, $g_aMissingPlayers))
		{
			$g_sPlayersNotFound .= "\n[$AuthID](https://steamcommunity.com/profiles/$AuthID)  ";
			array_push($g_aMissingPlayers, $AuthID);
		}
	}
	
}