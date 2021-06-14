<?php

include_once "helpers.php";

// ./main.php <host> <db in use> <user name> <DB password> <steam web API>
$host = $argv[1];
$database_in_use = $argv[2];
$username = $argv[3];
$user_pass = $argv[4];
$STEAM_WEB_API = $argv[5];
$port = "3306";

// steam web API used for getting player avatars
/* https://steamcommunity.com/dev/registerkey */
define('STEAM_WEB_API', $STEAM_WEB_API);

// Log files get organized as /log/Year/Month.log
$dirname = getcwd() . "/logs/" . date("Y") . "/";
if (!is_dir($dirname))
{
    mkdir($dirname, 0755, true);
}
// logs/2021/Feb.log
define('LOGNAME', $dirname . date("F") . ".log");

// logs/debug_output.log
define('DEBUG_LOGFILE', getcwd() . "/logs/debug_output.log");

// create PlayerList/ folder
$dirname = getcwd() . "/PlayerList/";
if (!is_dir($dirname))
{
    mkdir($dirname, 0755, true);
}

/* We initiliaze the connection to our server */
$mysqli = new mysqli($host, $username, $user_pass, $database_in_use, $port);

/* Echo out if there was an error while connecting */
if ($mysqli->connect_errno) 
{
	$logFile = new buildLogFile;
	$logFile->LogFatalError("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}