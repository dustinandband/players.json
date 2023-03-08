<?php

final class config extends logFile {
    private static $instance = null;
    private static $values = null;

    private function __clone(){}
    public function __wakeup(){}
    public function __destruct(){}
	
    private function __construct($host, $db, $user, $pass, $webAUTH) {
        $config['host'] = $host;
        $config['db'] = $db;
        $config['username'] = $user;
        $config['password'] = $pass;
        $config['port'] = "3306";
        $config['STEAM_WEB_API'] = $webAUTH;

        self::$values = $config;
    }

    private static function init() {
        if (self::$instance === null)
        {
			global $argv;
			
			$argCount = count($argv);
			if ($argCount !== 6)
			{
				$logFile = new logFile;
				$logFile->LogFatalError("Incorrect number of arguments (expected: 6, given: $argCount).\n\tUsage: $argv[0] <host> <db in use> <user name> <DB password> <steam web API>");
			}
			
			// ./main.php <host> <db in use> <user name> <DB password> <steam web API>
			$host = $argv[1];
			$database_in_use = $argv[2];
			$username = $argv[3];
			$user_pass = $argv[4];
			$port = "3306";
			$STEAM_WEB_API = $argv[5];
			
            self::$instance = new self($host, $database_in_use, $username, $user_pass, $STEAM_WEB_API);
        }
        return self::$instance;
    }

    public static function getValue($key)
    {
        self::init($key);
        return self::$values[$key];
    }
}