#!/usr/bin/env php

<?php

include_once "includes/autoloader.php";


main();

function main()
{
	/* iterate each table and correct names within the database */
	foreach(data::$db_tables as $table)
	{
		UpdatePlayerNamesInDB($table);
	}
}

function UpdatePlayerNamesInDB($table)
{
	$mysqli = connection::establishDBConnection();
	
	$sql_multi_query = "";
	$query_limit = 50;
	
	foreach (data::$player_aliases as $authID => $name)
	{
		if (in_array($authID, data::$steamid_ignore))
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
			$logFile = new logFile;
			$logFile -> LogFatalError("Table '$table' not yet defined in UpdatePlayerNamesInDB()");
		}
		
		// flush so the query doesn't get too big
		if (substr_count( $sql_multi_query, "\n" ) > $query_limit)
		{
			connection::multiquery_and_close_connection($sql_multi_query, true);
			$sql_multi_query = "";
		}
	}
	
	if (!empty($sql_multi_query))
	{
		connection::multiquery_and_close_connection($sql_multi_query, true);
		$sql_multi_query = "";
	}
	
	connection::KillDBConnection($mysqli);
}