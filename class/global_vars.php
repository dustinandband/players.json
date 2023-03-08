<?php

/* Used to contain globals within a class. 
	Provides some functions related to the globals too */
class global_vars {
	
	private static $values = null;
	private static $instance = null;
	
	private function __clone(){}
    public function __wakeup(){}
    public function __destruct(){}
	
	private function __construct()
	{
		// globals
		$values['steamIDs_OnLogAction_Logs'] = []; // OnLogAction_Logs table
		$values['steamIDs_SourceTV_Survival_LoggedEvents'] = []; // SourceTV_Survival_LoggedEvents table
		$values['SteamIDs_missingAliases'] = []; // aliases not yet defined in data::$player_aliases
		$values['steamIDs_everyone'] = []; // unique steamIDs of everyone in the DB
		self::$values = $values;
	}
	
	private static function init()
	{
		if (self::$instance === null)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	public static function PushValue($values_key, $authID, $bUniqueValuesOnly = false)
	{
		self::init();
		
		if (!array_key_exists($values_key, self::$values))
		{
			$logFile = new logFile();
			$logFile->LogFatalError("global_vars::PushValue(): Array key doesn't exist: '$values_key'");
		}
		
		if ($bUniqueValuesOnly)
		{
			if (in_array($authID, self::$values[$values_key]) === false)
			{
				array_push(self::$values[$values_key], $authID);
				return;
			}
		}
		array_push(self::$values[$values_key], $authID);
	}
	
	public static function getValue($key)
    {
        self::init($key);
		
		if (!array_key_exists($key, self::$values))
		{
			$logFile = new logFile();
			$logFile->LogFatalError("global_vars::GetValuesArray(): Array key doesn't exist: '$key'");
		}
		
        return self::$values[$key];
    }
	
	/*
		Missing Players array
	*/
	private static function InMissingPlayersArray($authID): bool
	{
		self::init();
		
		foreach (self::$values['SteamIDs_missingAliases'] as $index => $key)
		{
			if (in_array($authID, $key))
			{
				return true;
			}
		}
		return false;
	}
	
	public static function AddToMissingPlayersArray($authID, $bUniqueValuesOnly = true)
	{
		self::init();
		
		if ($bUniqueValuesOnly)
		{
			if (self::InMissingPlayersArray($authID))
			{
				return;
			}
		}
		
		$mysqli = connection::establishDBConnection();
		
		/* 	Do a query for every alias for the missingplayers.md table that gets generated */
		$query = "SELECT COUNT(*) FROM `SourceTV_Survival_Main` WHERE '$authID' IN (p1_authID, p2_authID, p3_authID, p4_authID);";
		if (!$result = $mysqli->query($query))
		{
			$count = $result = -1;
		}
		else
		{
			while($row = $result->fetch_assoc())
			{
				$count = $row['COUNT(*)'];
			}
		}
		
		array_push(self::$values['SteamIDs_missingAliases'], array('ID' => $authID, 'count' => $count));
		connection::KillDBConnection($mysqli);
	}
}