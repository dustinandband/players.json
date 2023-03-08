<?php

class logFile {

	private $log_general;
	private $log_errors;
	private $log_debugOutput;
	private $eventsLogged;
	private $errorsLogged;
	private $debugLogged;
	
	private $logFileName;
	private $Debug_logFileName;
	
	function __construct() {
		$this->log_general = "  Logged events:\n";
		$this->log_errors = "  Errors:\n";
		$this->log_debugOutput = "  Debug output:\n\n";
		$this->eventsLogged = $this->errorsLogged = $this->debugLogged = False;
		
		// Log files get organized as /log/Year/Month.log
		$dirname = getcwd() . "/logs/" . date("Y") . "/";
		if (!is_dir($dirname))
		{
			echo "Creating directory: $dirname" . PHP_EOL;
			mkdir($dirname, 0755, true);
		}

		$this->logFileName = $dirname . date("F") . ".log";
		$this->Debug_logFileName = $dirname . "debug_output" . ".log";
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
		
		$fp = fopen($this->logFileName, 'a');
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
		
		$fp = fopen($this->Debug_logFileName, 'w');
		$date = date("F j, Y, g:i a");
		fwrite($fp, "\n-------- $date --------\n\n");
		fwrite($fp, $this->log_debugOutput . "\n");
		fclose($fp);
	}
	
	public static function GenerateSteamIDsArray_jsonFile($value, $filename)
	{
		$SteamIDs = global_vars::getValue($value);
		if (empty($SteamIDs))
		{
			echo "logFile::GenerateSteamIDsArray_jsonFile: Empty array. Not generating SteamIDs.json" . PHP_EOL;
			return;
		}
		
		$dirname = getcwd() . "/logs/steamIDs/";
		if (!is_dir($dirname))
		{
			echo "Creating directory: $dirname" . PHP_EOL;
			mkdir($dirname, 0755, true);
		}
		
		file_put_contents($dirname . $filename, json_encode($SteamIDs));
	}
	
	public static function GenerateMissingPlayersFile()
	{
		$MissingPlayersArray = global_vars::getValue("SteamIDs_missingAliases");
		
		if (empty($MissingPlayersArray))
		{
			echo "logFile::GenerateMissingPlayersFile: Empty array. Not generating MissingPlayers.md" . PHP_EOL;
			return;
		}
		
		array_multisort( array_column($MissingPlayersArray, "count"), SORT_DESC, $MissingPlayersArray );
		
		$missingPlayersFile = getcwd() . "/logs/MissingPlayers.md";
		$fp = fopen($missingPlayersFile, 'w');
		$date = date("F j, Y, g:i a");
		fwrite($fp, "\n-------- $date --------  \n");
		fwrite($fp, "Consider adding the following steam64IDs to data::player_aliases:  \n");

		fwrite($fp, "\n| SteamID           | Rounds logged in SourceTV DB | Screenshots (verification) |  ");
		fwrite($fp, "\n|-------------------|------------------------------|------------------------------|  ");

		foreach ($MissingPlayersArray as $index => $key)
		{
			$auth64 = $key['ID'];
			$count = $key['count'];
			
			fwrite($fp, "\n| [$auth64](https://steamcommunity.com/profiles/$auth64) | $count                        | [link](https://steamcommunity.com/profiles/$auth64/screenshots/?appid=550&sort=newestfirst&browsefilter=myfiles&view=imagewall) |  ");
		}

		fclose($fp);
	}
}