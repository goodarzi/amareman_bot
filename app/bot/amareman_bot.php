<?php

require '../bootstrap.php';
header('Access-Control-Allow-Origin: null');
//date_default_timezone_set("UTC");
date_default_timezone_set('Asia/Tehran');

$telegram = new telegram('amareman_bot.conf');

try {
$DBHandle = $telegram->DbConnect();
} catch (Exception $e) {
	echo $e;
}


try {
	$telegram->handle();
	
	$telegram->log($telegram->input['message']['text']);
	
	$telegram->log($telegram->input['message']['from']['id']);
	
	$telegram_id = $telegram->input['message']['from']['id'];
	if (isset($telegram->input['message']['from']['first_name']))
	$user_first_name = $telegram->input['message']['from']['first_name'];
	
	if (isset($telegram->input['message']['from']['last_name']))
	$user_last_name = $telegram->input['message']['from']['last_name'];

	$user = check_existing_user($telegram_id) ;
	
	if ($user === false) {
		//new user
		new_user($telegram->input['message']['from']);
	} else {
		update_user($telegram->input['message']['from']);
	}
	
	
	if ($telegram->input['message']['entities'][0]['type'] == "bot_command") {
		$bot_command = substr($telegram->input['message']['text'], $telegram->input['message']['entities'][0]['offset'], $telegram->input['message']['entities'][0]['length']);
		$bot_command = strtolower($bot_command);

		if (($bot_command == "/start") && (strlen($telegram->input['message']['text']) == 20)) {
			
			$user_uniqid = substr($telegram->input['message']['text'],-13);
			$QUERY = "SELECT * FROM user WHERE uniqid = '" .$user_uniqid. "'";
			$result = $DBHandle->Execute($QUERY);
			if ($result === false) {
				$errMsg = "Error select prefix: " . $DBHandle->errorMsg();
			} else {
				$num_rows = $result->numRows();
				if ($num_rows > 0) {
					//vote to user $user_uniqid
					$target_user = $result->fetchRow();
					if ($target_user['id'] == $user['id']) {
						$send_message = "چه حسی به خودت داری گلم؟";
					} else {
						$send_message = "نظرت در مورد " . $target_user['first_name'] . " " . $target_user['last_name'] . " " . "چیه؟";
					}
					$replyMarkup = array(
						'keyboard' => array(array("👍", "❤️","👎","Cancel")),
						'one_time_keyboard' => true
					);
					$user_command['id'] = $user['id'];
					$user_command['command'] = 'vote';
					$user_command['command_level'] = 'new';
					$user_command['command_target'] = $target_user['id'];
					update_user($user_command);
					$telegram->sendMessage($send_message,null, $replyMarkup);
				} else {
					//invalid user
					$replyMarkup = array(
						'keyboard' => array(array("لینک خودم رو بده")),
						'one_time_keyboard' => true
					);
					$telegram->sendMessage("کسی رو برای این لینک پیدا نکردم.",false,$replyMarkup);
				}
			}
		} else { // switch bot commands
			switch($bot_command){
				
				case "/start";
					$replyMarkup = array(
					'keyboard' => array(array("ورود", "عضویت")),
					'one_time_keyboard' => true
					);
					sendMessage("سلام من روبات تلگام هستم!");
					break;
				case "/help";
					sendMessage("ایمیل آدرس عضویت در تلگام را وارد کنید.");
					break;
				default:
					
			}// END SWITCH BOT COMMANDS
		}
		
	} else {
		if (($user['command'] == 'vote') && ($user['command_level'] == 'new')) {
			switch($telegram->input['message']['text']) {
				case "❤️";
					$vote['user_id'] = $user['command_target'];
					$vote['from_user_id'] = $user['id'];
					$vote['vote_type_id'] = 1;
					set_vote($vote);
					
					$user_command['id'] = $user['id'];
					$user_command['command'] = 'vote';
					$user_command['command_level'] = 'created';
					$user_command['command_target'] = $target_user['id'];
					update_user($user_command);
					
					
					$send_message = " نظرت ثبت شد";
					$replyMarkup = array(
					'keyboard' => array(array("آمار خودم ❤️", "لینک خودم رو بده")),
					'one_time_keyboard' => true
					);
					
					$telegram->sendMessage($send_message,null, $replyMarkup);

					break;
				case "👍";
					$vote['user_id'] = $user['command_target'];
					$vote['from_user_id'] = $user['id'];
					$vote['vote_type_id'] = 2;
					set_vote($vote);
					
					$user_command['id'] = $user['id'];
					$user_command['command'] = 'vote';
					$user_command['command_level'] = 'created';
					$user_command['command_target'] = $target_user['id'];
					update_user($user_command);
					
					$send_message = " نظرت ثبت شد";
					$replyMarkup = array(
					'keyboard' => array(array("آمار خودم ❤️", "لینک خودم رو بده")),
					'one_time_keyboard' => true
					);
					
					$telegram->sendMessage($send_message,null, $replyMarkup);
					
					break;
				case "👎";
					$vote['user_id'] = $user['command_target'];
					$vote['from_user_id'] = $user['id'];
					$vote['vote_type_id'] = 3;
					set_vote($vote);
					
					$user_command['id'] = $user['id'];
					$user_command['command'] = 'vote';
					$user_command['command_level'] = 'created';
					$user_command['command_target'] = $target_user['id'];
					update_user($user_command);
					
					
					$send_message = " نظرت ثبت شد";
					$replyMarkup = array(
					'keyboard' => array(array("آمار خودم ❤️", "لینک خودم رو بده")),
					'one_time_keyboard' => true
					);
					
					$telegram->sendMessage($send_message,null, $replyMarkup);
					
					break;
				case "cancel";
					$user_command['id'] = $user['id'];
					$user_command['command'] = 'vote';
					$user_command['command_level'] = 'cancel';
					$user_command['command_target'] = '';
					update_user($user_command);
					
					$send_message = "کنسل شد.";
					$replyMarkup = array(
					'keyboard' => array(array("آمار خودم ❤️", "لینک خودم رو بده")),
					'one_time_keyboard' => true
					);
					
					break;
				default:
					update_user($user_command);
					
					$send_message = "ی نظر دادن فقط از 👎 ❤️ 👍 می تونی استفاده کنی گلم";
					
					$replyMarkup = array(
						'keyboard' => array(array("👍", "❤️","👎","Cancel")),
						'one_time_keyboard' => true
					);
					
					$telegram->sendMessage($send_message,null, $replyMarkup);
			}
		} else {
			switch($telegram->input['message']['text']) {
				case "آمار خودم ❤️";
				$send_message = check_vote($user['id']);
				$replyMarkup = array(
				'keyboard' => array(array("آمار خودم ❤️", "لینک خودم رو بده")),
				'one_time_keyboard' => false
				);
				$telegram->sendMessage($send_message,null, $replyMarkup);
				break;
				case "";
				break;
				case "";
				break;
				case "";
				break;
				default:
			}
		}
	}
	
	
	
} catch (Exception $e) {
	echo $e;
}

function check_existing_user($telegram_id) {
	$DBHandle = telegram::DbConnect();
	$QUERY = "SELECT * FROM user WHERE id = " . $telegram_id;
	$result = $DBHandle -> Execute($QUERY);
	if ($result === false) {
			$errMsg = "Error select prefix: " . $DBHandle->errorMsg();
		} else {
			$num_rows = $result->numRows();
			if ($num_rows > 0) {
				$user = $result->fetchRow();
				return $user;
			} else {
				return false;
			}
		}
}

function new_user($user){
	$DBHandle = telegram::DbConnect();
	$user['uniqid'] = uniqid();
	
	$table 	= 'user';
	$result = $DBHandle->AutoExecute($table,$user,'INSERT');
	
	if($result === false) {
		$errMsg = "Error creating new user: " . $DBHandle->errorMsg();
		//throw new Exception($errMsg);
	}
	$userSysId = $DBHandle -> insert_id();
}

function update_user($user){
	$DBHandle = telegram::DbConnect();
	$user['updated_at'] = date("Y-m-d H:i:s", time());
	$table 	= 'user';
	$where 	= "id = " . $user['id'];
	$result = $DBHandle->AutoExecute($table,$user,'UPDATE',$where);
}

/*
$vote['user_id'] vote : for this user.
$vote['from_user_id'] : vote from this user.
$vote['vote_type_id'] : 1 = ❤️ , 2 = 👍 , 3 = 👎
*/
function set_vote($vote) {
	
	$DBHandle = telegram::DbConnect();
	$QUERY = "SELECT id FROM vote WHERE user_id = ".$vote['user_id']." AND from_user_id = " . $vote['from_user_id'];
	$result = $DBHandle -> Execute($QUERY);
	$num_rows = $result->numRows();
	if ($num_rows > 0) {
		//UPDATE VOTE
		$vote = $result->fetchRow();
		$where 	= "id = " . $vote['id'];
		$action = "UPDATE";
	} else {
		$where = null;
		$action = "INSERT";
	}
	
	$table 	= 'vote';
	$result = $DBHandle->AutoExecute($table,$vote,$action,$where);
	
	if($result === false) {
		$errMsg = "Error creating new user: " . $DBHandle->errorMsg();
		//throw new Exception($errMsg);
	}
}

function check_vote($user_id) {
	$DBHandle = telegram::DbConnect();
	$QUERY = "SELECT SUM(vote_type_id = 1) AS loveCount,".
			" SUM(userID_following = 2) AS likeCount,".
			" SUM(userID_following = 3) AS dislikeCount,".
			" FROM t1".
			" WHERE user_id = " . $user_id;
	$result = $DBHandle->Execute($QUERY);
	if($result === false) {
		$errMsg = "Error counting votes: " . $DBHandle->errorMsg();
		//throw new Exception($errMsg);
	} else {
		$num_rows = $result->numRows();
		if ($num_rows > 0) {
			$vote = $result->fetchRow();
			return $vote;
		} else {
			$vote = "هنوز هیچ نظری برات نیومده!";
			return $vote;
		}
	}
}

?>