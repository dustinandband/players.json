<?php

include_once "helpers.php";

// steam web API used for getting player avatars
/* https://steamcommunity.com/dev/registerkey */
define('STEAM_WEB_API', $argv[2]);
define('LOGNAME', 'LogFile.log');

$user_pass = $argv[1];

/* database login information */
$host = "fortestingstuf.site.nfoservers.com";
$username = "fortestingstuf";
$user_pass = $user_pass;
$database_in_use = "fortestingstuf_MainDB";
$port = "3306";

/* We initiliaze the connection to our server */
$mysqli = new mysqli($host, $username, $user_pass, $database_in_use, $port);

/* Echo out if there was an error while connecting */
if ($mysqli->connect_errno) 
{
	$logFile = new buildLogFile;
	$logFile->LogFatalError("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}
