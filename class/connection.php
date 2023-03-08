<?php

class connection extends logFile {
	
	function __construct($host, $username, $user_pass, $DB_in_use, $port = "3306") {
		$this->host = $host;
		$this->username = $username;
		$this->user_pass = $user_pass;
		$this->DB_in_use = $DB_in_use;
		$this->port = $port;
	}
	
	public static function establishDBConnection()
	{
		$host = config::getValue('host');
		$username = config::getValue('username');
		$password = config::getValue('password');
		$db_in_use = config::getValue('db');
		$port = config::getValue('port');
		
		$mysqli = new mysqli($host, $username, $password, $db_in_use, $port);

		if ($mysqli->connect_errno) 
		{
			$logFile = new logFile;
			$logFile->LogFatalError("Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
		}
		return $mysqli;
	}
	
	public static function KillDBConnection($mysqli) {
		if ($mysqli !== null) 
		{
			$mysqli->close();
		}
	}
	
	
	/*
	It takes longer to iterate result set than to simply open and close the connection
		when using multi_query method.
	*/
	public static function multiquery_and_close_connection($query, $print_multiquery = false, $delay_timer = 12)
	{
		$db_conn = self::establishDBConnection();
		$db_conn->multi_query($query);
		self::KillDBConnection($db_conn);
		
		if ($print_multiquery === true)
		{
			echo "multi_query output: \n$query" . PHP_EOL;
		}
		
		// starts boggling down the DB if you don't give it a few seconds to process
		echo "multiquery_and_close_connection(): Waiting $delay_timer seconds for DB to finish processing queries." . PHP_EOL;
		sleep($delay_timer);
	}
	
}