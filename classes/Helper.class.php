<?php

/**
 *
 * @author     Alexander Linke <api@highor.com>
 * @copyright  2016 HighorBV
 *
 **/

class Helper {

	CONST DBFILE = 'config/database.ini';
	private $data = array();

	public function callType() {
		if (preg_match('^\/v[0-9]{1,6}\/^', $_SERVER['REQUEST_URI'])) {
			return 'api';
		}
		return 'http';
	}

	public function redirect($to) {
		header('Location: /'.$to);
		exit;
	}

	public function logout() {
		session_start();
		unset($_SESSION);
		session_destroy();
		$this->redirect('login');
	}

	public function validateCreateAPICall($data, Database $database) {
		if (!array_key_exists('apiurl', $data) OR trim($data['apiurl']) == '' OR !array_key_exists('apitype', $data) OR trim($data['apitype']) == '' OR !array_key_exists('apifile', $data) OR trim($data['apifile']) == '') {
			return $this->addMessage('All fields are required','error');
		} else if (!file_exists($data['apifile'])) {
			return $this->addMessage('File does not exist: '.$data['apifile'],'info');
		} else if ($database->checkAPIUrl($data) === false) {
			return $this->addMessage('Api url already exists', 'error');
		} else if (preg_match('/^[a-zA-Z0-9\/]+$/', $data['apiurl']) == false) {
			return $this->addMessage('Only letters, numbers and slashes are allowed in the api url', 'error');
		}

		return true;
	}

	public function validateCreateAppForm($data, Database $database) {
		if (!array_key_exists('appname', $data) OR !array_key_exists('authkey', $data) OR !array_key_exists('authuser', $data) OR trim($data['authuser']) == '' OR trim($data['appname']) == '' OR trim($data['authkey']) == '') {
			return $this->addMessage("App name and basic authentication login is required", 'error');
		} else if ($database->checkAppCredentionals($data) === false) {
			return $this->addMessage("Make sure app name and basic authentication password does not exists", 'error');
		} else if (preg_match('/^[a-zA-Z0-9]+$/', $data['appname']) == false) {
			return $this->addMessage('Only letters and numbers are allowed in the app name', 'error');
		}

		return true;
	}

	public function isLoggedIn(Database $database) {
		session_start();
		if (array_key_exists('login', $_SESSION) AND array_key_exists('user_id', $_SESSION['login']) AND array_key_exists('hash', $_SESSION['login']) AND is_numeric(trim($_SESSION['login']['user_id'])) AND trim($_SESSION['login']['hash']) != '') {
			if ($database->checkCredentionals($_SESSION['login'])) {
				return true;
			}
		}

		session_destroy();
		$this->redirect('login');
	}

	public function validateLogin($data, Database $database) {
		if (!array_key_exists('adminusername', $data) OR !array_key_exists('adminpassword', $data) OR trim($data['adminusername']) == '' OR trim($data['adminpassword']) == '') {
			return $this->addMessage("Username and Password is required", 'error');
		} elseif ($database->login($data) === false) {
			return $this->addMessage("Login is incorrect", 'error');
		}

		return true;
	}

	public function validateCreateForm($data, Database $database) {
		if (!array_key_exists('adminpassword', $data) OR !array_key_exists('adminrpassword', $data) OR trim($data['adminpassword']) == '' OR trim($data['adminrpassword']) == '') {
			return $this->addMessage("Admin password must be filled in", 'error');
		} else if (array_key_exists('adminpassword', $data) AND array_key_exists('adminrpassword', $data) AND trim($data['adminpassword']) != trim($data['adminrpassword'])) {
			return $this->addMessage("Admin password did not match", 'error');
		} else if (!array_key_exists('adminusername', $data) OR trim($data['adminusername']) == '') {
			return $this->addMessage("Admin username is required", 'error');
		} else if (array_key_exists('username', $data)) {
			$response = $database->try($data);
			if ($response !== true) {
				return $this->addMessage($response, 'error');
			}
		}

		return true;
	}

	public function saveDBConfig($data) {
		$current = file_get_contents(self::DBFILE);
		$current = "hostname=".$data['hostname']."\n";
		$current .= "username=".$data['username']."\n";
		$current .= "password=".$data['password']."\n";
		$current .= "dbname=api";
		file_put_contents(self::DBFILE, $current);
	}

	public function validDBConnection(Database $database) {
		$dbData = parse_ini_file(self::DBFILE);

		if (!array_key_exists('username', $dbData) OR trim($dbData['username']) == ''
			OR !array_key_exists('password', $dbData) OR trim($dbData['password']) == ''
			OR !array_key_exists('hostname', $dbData) OR trim($dbData['hostname']) == '') {
			return false;
		}

		return $database->try($dbData);
	}

	public function getValue($value) {
		if (array_key_exists($value, $_REQUEST)) {
			return $_REQUEST[$value];
		}
		return '';
	}

	private function _createDefaultData() {
		if (!is_array($this->data)) {
			$this->data = array();
			$this->data['messages'] = array();
			$this->data['messages']['success'] = array();
			$this->data['messages']['info'] = array();
			$this->data['messages']['error'] = array();
		}
		
		return $this->data;
	}

	public function addMessage($message, $type = 'error') {
		$this->data = $this->_createDefaultData();
		$this->data['messages'][$type][] = $message;
		return $this->data;
	}

	public function renderMessages() {
		if (array_key_exists('messages', $this->data)) {
			if (array_key_exists('success', $this->data['messages']) and count($this->data['messages']['success']) > 0) {
				foreach ($this->data['messages']['success'] as $message) {
					echo '<div class="alert alert-success" role="alert">'.$message.'</div>';
				}
			}
			if (array_key_exists('info', $this->data['messages']) and count($this->data['messages']['info']) > 0) {
				foreach ($this->data['messages']['info'] as $message) {
					echo '<div class="alert alert-warning" role="alert">'.$message.'</div>';
				}
			}
			if (array_key_exists('error', $this->data['messages']) and count($this->data['messages']['error']) > 0) {
				foreach ($this->data['messages']['error'] as $message) {
					echo '<div class="alert alert-danger" role="alert">'.$message.'</div>';
				}
			}
		}
	}

}