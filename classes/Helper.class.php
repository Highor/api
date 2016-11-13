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
		return (1 == 1 ? 'http' : 'api');
	}

	public function redirect($to) {
		header('Location: /'.$to);
		exit;
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

	public function addMessage($message, $type) {
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