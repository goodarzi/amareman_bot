<?php
class Telegram {

protected $bot_config_file = '';
protected $config;

	public function __construct($bot_config_file) {
		
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$config_dir = 'C:\\REPO\\amareman_bot\\';
		} else {
			$config_dir = '/etc/';
		}
		
		if (file_exists($config_dir.$bot_config_file)) {
			$config = parse_ini_file($config_dir.$bot_config_file, true);
			
			// conf for the database connection
			if (isset($config['database']['hostname']))		$this->config['database']['hostname'] = $config['database']['hostname'];
			if (isset($config['database']['user']))		$this->config['database']['user']     = $config['database']['user'];
			if (isset($config['database']['password']))	$this->config['database']['password'] = $config['database']['password'];
			if (isset($config['database']['dbname']))		$this->config['database']['dbname']   = $config['database']['dbname'];
			
			if (!isset($config['telegram']['token']) || empty($config['telegram']['token'])) {
				$errMsg = "Error : Bot token not found in '" . $config_dir.$bot_config_file . "'.";
				throw new Exception($errMsg);
			} else {
				$this->config['telegram']['token'] = $config['telegram']['token'];
			}
			
			if (isset($config['telegram']['logfile']) && !empty($config['telegram']['logfile']))
				$this->config['telegram']['logfile']   = $config['telegram']['logfile'];
			else
				$this->config['telegram']['logfile']   = 'bot.log';
			
			if (isset($config['telegram']['logenable']) && !empty($config['telegram']['logenable']))
				$this->config['telegram']['logenable']   = $config['telegram']['logenable'];
			else
				$this->config['telegram']['logenable']   = false;
			
		} else {
			$errMsg = "Error : Configuration file is missing! '" . $config_dir.$bot_config_file . "' Not found.";
			throw new Exception($errMsg);
		}
	}


public function DbConnect() {
    return Connection::GetDBHandler($this->config['database']);
}

public function DbDisconnect($DBHandle) {
    $DBHandle ->disconnect();
}

public function handle() {
	
	$input = file_get_contents('php://input');
	
	if (is_string($input) | $input == false) {
		$this->rawInput = $input;
		$this->log('rawInput: '.$input);
	} else {
		throw new Exception('Input must be a string!');
	}
	
	if (empty($this->rawInput)) {
		throw new Exception('Input is empty!');
	}
	
	$post = json_decode($this->rawInput, true);
	if (empty($post)) {
		throw new Exception('Invalid JSON!');
	} else {
		$this->input = $post;
		$this->log('input: '.$post);
	}
	
}

	public function log($text) {
		
		if ($this->config['telegram']['logenable']) {
			return file_put_contents(
				$this->config['telegram']['logfile'],
				date('Y-m-d H:i:s', time()) . ' ' . $text . "\n",
				FILE_APPEND
			);
		}
		return 0;
	}

	
}


?>