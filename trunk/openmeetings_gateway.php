<?php
/*
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

require_once($CFG->dirroot.'/mod/openmeetings/lib/openmeetings_rest_service.php');

class openmeetings_gateway {

	var $session_id = "";
	
	function getUrl() {
		global $CFG;
		//FIXME protocol should be added
		$port = $CFG->openmeetings_red5port == 80 ? '' : ":" . $CFG->openmeetings_red5port;
		return "http://" . $CFG->openmeetings_red5host . $port . "/" . $CFG->openmeetings_webappname;
	}

	function var_to_str($in)
	{
		if(is_bool($in))
		{
			if($in)
			return "true";
			else
			return "false";
		}
		else
		return $in;
	}


	/**
	 * TODO: Get Error Service and show detailed Error Message
	 */

	function openmeetings_loginuser() {
		global $CFG;

		$restService = new openmeetings_rest_service();

		$response = $restService->call($this->getUrl()."/services/UserService/getSession","session_id");

		if ($restService->getError()) {
			echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
		} else {
			$err = $restService->getError();
			if ($err) {
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} else {
				$this->session_id = $response;

				$result = $restService->call($this->getUrl()."/services/UserService/loginUser?"
				. "SID=".$this->session_id
				. "&username=" . urlencode($CFG->openmeetings_openmeetingsAdminUser)
				. "&userpass=" . urlencode($CFG->openmeetings_openmeetingsAdminUserPass)
				);

				if ($restService->getError()) {
					echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
				} else {
					$err = $restService->getError();
					if ($err) {
						echo '<h2>Error</h2><pre>' . $err . '</pre>';
					} else {
						$returnValue = $result;
					}
				}
			}
		}
		
		if ($returnValue>0){
			return true;
		} else {
			return false;
		}
	}


	function openmeetings_updateRoomWithModeration($openmeetings) {

		global $CFG;

		$restService = new openmeetings_rest_service();
		//echo $restService."<br/>";
		$err = $restService->getError();
		if ($err) {
			echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
			echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
			exit();
		}
		$course_name = 'MOODLE_COURSE_ID_'.$openmeetings->course.'_NAME_'.$openmeetings->name;
			
		$isModeratedRoom = false;
		if ($openmeetings->is_moderated_room == 1) {
			$isModeratedRoom = true;
		}

		$result = $restService->call($this->getUrl()."/services/RoomService/updateRoomWithModeration?" .
							"SID=".$this->session_id.
							"&room_id=".$openmeetings->room_id.
							"&name=".urlencode($course_name).
							"&roomtypes_id=".urlencode($openmeetings->type).
							"&comment=".urlencode("Created by SOAP-Gateway for Moodle Platform").
							"&numberOfPartizipants=".$openmeetings->max_user.
							"&ispublic=false".
							"&appointment=false".
							"&isDemoRoom=false".
							"&demoTime=0".
							"&isModeratedRoom=".$this->var_to_str($isModeratedRoom));

		if ($restService->fault) {
			echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
		} else {
			$err = $restService->getError();
			if ($err) {
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} else {
				//echo '<h2>Result</h2><pre>'; print_r($result["return"]); echo '</pre>';
				return $result;
			}
		}
		return -1;
	}

	/*
	 * public String setUserObjectAndGenerateRecordingHashByURL(String SID, String username, String firstname, String lastname,
					Long externalUserId, String externalUserType, Long recording_id)
	 */
	 function openmeetings_setUserObjectAndGenerateRecordingHashByURL($username, $firstname, $lastname, 
						$userId, $systemType, $recording_id) {
	    $restService = new openmeetings_rest_service();
	 	$result = $restService->call($this->getUrl().'/services/UserService/setUserObjectAndGenerateRecordingHashByURL?'.
			'SID='.$this->session_id .
			'&username='.urlencode($username) .
			'&firstname='.urlencode($firstname) .
			'&lastname='.urlencode($lastname) .
			'&externalUserId='.$userId .
			'&externalUserType='.urlencode($systemType) .
			'&recording_id='.$recording_id,
			'return'
			);
		
		if ($client_roomService->fault) {
			echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
		} else {
			$err = $restService->getError();
			if ($err) {
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} else {
				return $result;
			}
		}   
		return -1;
	}

	function openmeetings_setUserObjectAndGenerateRoomHashByURLAndRecFlag($username, $firstname, $lastname,
					$profilePictureUrl, $email, $userId, $systemType, $room_id, $becomeModerator, $allowRecording) {
		global $CFG;

		$restService = new openmeetings_rest_service();
		//echo $restService."<br/>";
		$err = $restService->getError();
		if ($err) {
			echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
			echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
			exit();
		}

		$result = $restService->call($this->getUrl()."/services/UserService/setUserObjectAndGenerateRoomHashByURLAndRecFlag?" .
							"SID=".$this->session_id.
							"&username=".urlencode($username).
							"&firstname=".urlencode($firstname).
							"&lastname=".urlencode($lastname).
							"&profilePictureUrl=".urlencode($profilePictureUrl).
							"&email=".urlencode($email).
							"&externalUserId=".urlencode($userId).
							"&externalUserType=".urlencode($systemType).
							"&room_id=".urlencode($room_id).
							"&becomeModeratorAsInt=".$becomeModerator.
							"&showAudioVideoTestAsInt=1".
							"&allowRecording=".$this->var_to_str($allowRecording));

		if ($restService->fault) {
			echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
		} else {
			$err = $restService->getError();
			if ($err) {
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} else {
				//echo '<h2>Result</h2><pre>'; print_r($result["return"]); echo '</pre>';
				return $result;
			}
		}
		return -1;
	}

	function deleteRoom($openmeetings) {
		global $CFG;

		//echo $client_roomService."<br/>";
		$restService = new openmeetings_rest_service();
		$err = $restService->getError();
		if ($err) {
			echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
			echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
			exit();
		}

		$result = $restService->call($this->getUrl()."/services/RoomService/deleteRoom?" .
							"SID=".$this->session_id.
							"&rooms_id=".$openmeetings->room_id);

		if ($restService->fault) {
			echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
		} else {
			$err = $restService->getError();
			if ($err) {
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} else {
				//echo '<h2>Result</h2><pre>'; print_r($result["return"]); echo '</pre>';
				//return $result["return"];
				return $result;
			}
		}
		return -1;
	}


	/**
	 * Generate a new room hash for entering a conference room
	 */
	function openmeetings_setUserObjectAndGenerateRoomHash($username,
									$firstname,
									$lastname,
									$profilePictureUrl,
									$email,
									$externalUserId,
									$externalUserType,
									$room_id,
									$becomeModeratorAsInt,
									$showAudioVideoTestAsInt) {

		global $CFG;

		$restService = new openmeetings_rest_service();

		$result = $restService->call($this->getUrl()."/services/UserService/setUserObjectAndGenerateRoomHash?" .
					"SID=".$this->session_id.
					"&username=".urlencode($username).
					"&firstname=".urlencode($firstname).
					"&lastname=".urlencode($lastname).
					"&profilePictureUrl=".urlencode($profilePictureUrl).
					"&email=".urlencode($email).
					"&externalUserId=".urlencode($externalUserId).
					"&externalUserType=".urlencode($externalUserType).
					"&room_id=".$room_id.
					"&becomeModeratorAsInt=".$becomeModeratorAsInt.
					"&showAudioVideoTestAsInt=".$showAudioVideoTestAsInt);


		$err = $restService->getError();
		if ($err) {
			echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
			echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
			exit();
		}

		if ($restService->getError()) {
			echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
		} else {
			$err = $restService->getError();
			if ($err) {
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} else {
				//echo '<h2>Result</h2><pre>'; print_r($result["return"]); echo '</pre>';
				return $result;

			}
		}
		return -1;
	}
	
	/**
	 * Create a new conference room
	 */
	function openmeetings_createRoomWithModAndType($openmeetings) {
		global $USER, $CFG;
	
		$restService = new openmeetings_rest_service();
    	$course_name = 'MOODLE_COURSE_ID_'.$openmeetings->course.'_NAME_'.$openmeetings->name;
		
		$isModeratedRoom = "false";
		if ($openmeetings->is_moderated_room == 1) {
			$isModeratedRoom = "true";
		}
		
		$url = $this->getUrl().'/services/RoomService/addRoomWithModerationAndExternalType?' .
						'SID='.$this->session_id .
						'&name='.urlencode($course_name).
						'&roomtypes_id='.$openmeetings->type .
						'&comment='.urlencode('Created by SOAP-Gateway for Moodle Platform') .
						'&numberOfPartizipants='.$openmeetings->max_user .
						'&ispublic=false'.
						'&appointment=false'.
						'&isDemoRoom=false'.
						'&demoTime=0' .
						'&isModeratedRoom='.$isModeratedRoom .
						'&externalRoomType='.urlencode($CFG->openmeetings_openmeetingsModuleKey)
						;
		
	 	$result = $restService->call($url, "return");
		
		if ($restService->fault) {
			echo '<h2>Fault (Expect - The request contains an invalid SOAP body)</h2><pre>'; print_r($result); echo '</pre>';
		} else {
			$err = $restService->getError();
			if ($err) {
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} else {
				return $result;
			}
		}   
		return -1;
	}

	/**
	 * Get list of available recordings made by this Moodle instance
	 */
	function openmeetings_getRecordingsByExternalRooms() {
	
		global $CFG;

		$restService = new openmeetings_rest_service();
		
		$url = $this->getUrl()."/services/RoomService/getFlvRecordingByExternalRoomType?" .
					"SID=".$this->session_id .
					"&externalRoomType=".urlencode($CFG->openmeetings_openmeetingsModuleKey);

		$result = $restService->call($url,"");
					
		return $result;		
					
	}

}

?>
