<?php

include_once "../helpers.php";

// ./main.php <DB password> <steam web API> <user name> <db in use> <host>
$user_pass = $argv[1];
$STEAM_WEB_API = $argv[2];
$username = $argv[3];
$database_in_use = $argv[4];
$host = $argv[5];
$port = "3306";

// steam web API used for getting player avatars
/* https://steamcommunity.com/dev/registerkey */
define('STEAM_WEB_API', $STEAM_WEB_API);
define('LOGNAME', 'LogFile.log');

/* We initiliaze the connection to our server */
$mysqli2 = new mysqli($host, $username, $user_pass, $database_in_use, $port);

/* Echo out if there was an error while connecting */
if ($mysqli2->connect_errno) 
{
	$logFile = new buildLogFile;
	$logFile->LogFatalError("GM activity log: Failed to connect to MySQL: (" . $mysqli2->connect_errno . ") " . $mysqli2->connect_error);
}