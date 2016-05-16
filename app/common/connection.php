<?php

class Connection
{
	private static $DBHandler;
	
	private function __construct($database)
	{
		$ADODB_CACHE_DIR = '/tmp';
        /*	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;	*/

		$datasource = 'mysqli://' . $database['user'] . ':' . $database['password'] . '@' . $database['hostname'] . '/' . $database['dbname'];
		
		$DBHandle = NewADOConnection($datasource);
		
		if (!$DBHandle) {
			throw new Exception("Connection failed");
			return false;
		}

		$DBHandle->Execute('SET AUTOCOMMIT=1');
		$DBHandle->Execute("SET NAMES 'UTF8'");

		self :: $DBHandler = $DBHandle;
    }

	public static function GetDBHandler($database)
	{
		if (empty (self :: $DBHandler)) {
			$connection = new Connection($database);
		}
		return self :: $DBHandler;
	}

	public static function CleanExecute($QUERY)
	{
		if (empty (self :: $DBHandler)) {
			$connection = new Connection();
		} else {
			$connection = self :: $DBHandler;
		}

		return $connection -> Execute($QUERY);
	}
}
?>