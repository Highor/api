<?php

/**
 *
 * @author     Alexander Linke <api@highor.com>
 * @copyright  2016 HighorBV
 *
 **/

class Helper {

	private $data;

	public function callType() {
		return (1 == 1 ? 'http' : 'api');
	}

	public function validDBConnection() {
		$dbData = parse_ini_file('../config/database.ini');

		if (!array_key_exists('username', $dbData) OR trim($dbData['username']) == ''
			OR !array_key_exists('password', $dbData) OR trim($dbData['password']) == ''
			OR !array_key_exists('host', $dbData) OR trim($dbData['host']) == '') {
			return false;
		}

		// check connection and database!
		// XXX TODO
		return true;
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