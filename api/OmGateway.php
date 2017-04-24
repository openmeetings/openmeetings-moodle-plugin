<?php
/**
 * you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * It is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You can get a copy of the GNU General Public License
 * at <http://www.gnu.org/licenses/>.
 */
/**
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License") +  you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */

require_once ('OmRestService.php');

class OmGateway {
	var $sessionId = "";
	var $config = array();

	function __construct($cfg) {
		$this->config = $cfg;
	}

	function getRestUrl($name) {
		return $this->getUrl() . "/services/" . $name . "/";
	}

	function getUrl() {
		$port = $this->config["port"] == 80 ? '' : ":" . $this->config["port"];
		return $this->config["protocol"] . "://" . $this->config["host"] . $port . "/" . $this->config["context"];
	}

	function version() {
		$rest = new OmRestService();
		$response = $rest->call(
				$this->getRestUrl("info") . "version"
				, RestMethod::GET
				, null
				, array()
				, null
				, "info"
				);
		return $response;
	}

	function login() {
		$rest = new OmRestService();
		$response = $rest->call(
				$this->getRestUrl("user") . "login"
				, RestMethod::GET
				, null
				, array("user" => $this->config["user"], "pass" => $this->config["pass"])
				, null
				, "serviceResult"
			);

		if ($rest->isError()) {
			echo '<h2>Fault (Service error)</h2><pre>';
			print_r($rest->getMessage());
			echo '</pre>';
		} else {
			if ($response["type"] == "SUCCESS") {
				$this->sessionId = $response["message"];
				return true;
			} else {
				echo '<h2>Error While signing into OpenMeetings, please check credentials</h2><pre>' . $response["code"] . '</pre>';
			}
		}
		return false;
	}
	
	function getUser($login, $firstname, $lastname, $profilePictureUrl, $email, $userId) {
		return array(
			"login" => $login
			, "firstname" => $firstname
			, "lastname" => $lastname
			, "email" => $email
			, "externalId" => $userId
			, "externalType" => $this->config["module"]
		);
	}
	
	function getSecureHash($user, $options) {
		$rest = new OmRestService();
		$response = $rest->call(
				$this->getRestUrl("user") . "hash"
				, RestMethod::POST
				, $this->sessionId
				, http_build_query(array("user" => json_encode($user), "options" => json_encode($options)), '', '&')
				, null
				, "serviceResult"
			);

		if ($rest->isError()) {
			echo '<h2>Fault (Service error)</h2><pre>';
			print_r($rest->getMessage());
			echo '</pre>';
		} else {
			if ($response["type"] == "SUCCESS") {
				return $response["message"];
			} else {
				echo '<h2>Error While signing into OpenMeetings, please check credentials</h2><pre>' . $response["code"] . '</pre>';
			}
		}
		return -1;
	}

	function getRoom($roomId) {
		$rest = new OmRestService();
		$response = $rest->call(
				$this->getRestUrl("room") . $roomId
				, RestMethod::GET
				, $this->sessionId
				, null
				, null
				, "roomDTO"
			);
		if ($rest->isError()) {
			echo '<h2>Fault (Service error)</h2><pre>';
			print_r($rest->getMessage());
			echo '</pre>';
		} else {
			if (isset($response["id"]) && $response["id"]) {
				return $response;
			} else {
				echo '<h2>Error While signing into OpenMeetings, please check credentials</h2><pre>' . $response["code"] . '</pre>';
			}
		}
		return -1;
	}

	function updateRoom($data) {
		$data['externalType'] = $this->config["module"];
		$rest = new OmRestService();
		$response = $rest->call(
				$this->getRestUrl("room")
				, RestMethod::POST
				, $this->sessionId
				, array('room' => json_encode($data))
				, null
				, "roomDTO"
			);
		if ($rest->isError()) {
			echo '<h2>Fault (Service error)</h2><pre>';
			print_r($rest->getMessage());
			echo '</pre>';
		} else {
			if ($response["id"] > 0) {
				return $response["id"];
			} else {
				echo '<h2>Error While signing into OpenMeetings, please check credentials</h2><pre>' . $response["code"] . '</pre>';
			}
		}
		return -1;
	}

	function deleteRoom($roomId) {
		$rest = new OmRestService();
		$response = $rest->call(
				$this->getRestUrl("room") . $roomId
				, RestMethod::DELETE
				, $this->sessionId
				, ""
				, null
				, "serviceResult"
			);
		if ($rest->isError()) {
			echo '<h2>Fault (Service error)</h2><pre>';
			print_r($rest->getMessage());
			echo '</pre>';
		} else {
			if ($response["type"] == "SUCCESS") {
				return $response["code"];
			} else {
				echo '<h2>Error While signing into OpenMeetings, please check credentials</h2><pre>' . $response["code"] . '</pre>';
			}
		}
		return -1;
	}

	/**
	 * Get list of available recordings made by this instance
	 */
	function getRecordings() {
		$rest = new OmRestService();
		$response = $rest->call(
				$this->getRestUrl("record") . urlencode($this->config["module"])
				, RestMethod::GET
				, $this->sessionId
				, ""
				, null
				, "recordingDTO"
			);
		if ($rest->isError()) {
			echo '<h2>Fault (Service error)</h2><pre>';
			print_r($rest->getMessage());
			echo '</pre>';
		} else {
			return $response;
		}
		return array();
	}

	function deleteRecording($recId) {
		$rest = new OmRestService();
		$response = $rest->call(
				$this->getRestUrl("record") . $recId
				, RestMethod::DELETE
				, $this->sessionId
				, ""
				, null
				, "serviceResult"
			);
		if ($rest->isError()) {
			echo '<h2>Fault (Service error)</h2><pre>';
			print_r($rest->getMessage());
			echo '</pre>';
		} else {
			if ($response["type"] == "SUCCESS") {
				return $response["code"];
			} else {
				echo '<h2>Error While signing into OpenMeetings, please check credentials</h2><pre>' . $response["code"] . '</pre>';
			}
		}
		return -1;
	}

	function createFile($fileJson, $file) {
		$rest = new OmRestService();
		$boundary = '';
		$params = array(
				array(
					"name" => "file"
					, "type" => "application/json"
					, "val" => json_encode($fileJson)
				)
				, array(
					"name" => "stream"
					, "type" => "application/octet-stream"
					, "val" => file_get_contents($file)
				)
			);
		$data = OmRestService::encode($params, $boundary);
		$response = $rest->call(
				$this->getRestUrl("file")
				, RestMethod::POST
				, $this->sessionId
				, $data
				, array("Content-Length: " . strlen($data), 'Content-Type: multipart/form-data; boundary=' . $boundary)
				, "fileExplorerItemDTO"
				);
		if ($rest->isError()) {
			echo '<h2>Fault (Service error)</h2><pre>';
			print_r($rest->getMessage());
			echo '</pre>';
		} else {
			return $response;
		}
		return array();
	}
}
