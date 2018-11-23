<?php
/*
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 */
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

set_error_handler("myErrorHandler");
require_once($CFG->dirroot.'/config.php');
require_once($CFG->dirroot.'/mod/openmeetings/api/OmGateway.php');

// error handler function
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
	switch ($errno) {
		case E_USER_ERROR:
			die("<b>My ERROR</b> [$errno] $errstr<br />\n"
				. "  Fatal error on line $errline in file $errfile"
				. ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n"
				. "Aborting...<br />\n");
		case E_USER_WARNING:
			echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
			break;
		case E_USER_NOTICE:
			echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
			break;
		default:
			//echo "Unknown error type: [$errno] $errstr<br />\n";
			break;
	}
	/* Don't execute PHP internal error handler */
	return true;
}

function getOmUser($gateway) {
	global $USER;
	$pictureUrl = moodle_url::make_pluginfile_url(context_user::instance($USER->id)->id, 'user', 'icon', NULL, '/', 'f2')->out(false);
	return $gateway->getUser($USER->username, $USER->firstname, $USER->lastname, $pictureUrl, $USER->email, $USER->id);
}

function getOmHash($gateway, $options) {
	return $gateway->getSecureHash(getOmUser($gateway), $options);
}

function getOmConfig() {
	global $CFG;
	return array(
			"protocol" => $CFG->openmeetings_protocol,
			"host" => $CFG->openmeetings_host,
			"port" => $CFG->openmeetings_port,
			"context" => $CFG->openmeetings_context,
			"user" => $CFG->openmeetings_user,
			"pass" => $CFG->openmeetings_pass,
			"module" => $CFG->openmeetings_moduleKey,
			"checkpeer" => 1 == $CFG->openmeetings_checkpeer,
			"checkhost" => 1 == $CFG->openmeetings_checkhost,
			"debug" => $CFG->debug > 0
	);
}

function setRoomName(&$openmeetings) {
	$openmeetings->roomname = 'MOODLE_COURSE_ID_' . $openmeetings->course . '_NAME_' . $openmeetings->name;
}

function getRoom(&$meeting) {
	setRoomName($meeting);
	return array(
			'id' => $meeting->room_id > 0 ? $meeting->room_id : null
			, 'name' => $meeting->roomname
			, 'comment' => 'Created by SOAP-Gateway'
			, 'type' => $meeting->type
			, 'capacity' => $meeting->max_user
			, 'isPublic' => false
			, 'appointment' => false
			, 'moderated' => 1 == $meeting->is_moderated_room
			, 'audioOnly' => false
			, 'allowUserQuestions' => true
			, 'allowRecording' => 1 == $meeting->allow_recording
			, 'chatHidden' => 1 == $meeting->chat_hidden
			, 'externalId' => $meeting->id
			, 'files' => array()
	);
}

function openmeetings_add_instance(&$meeting) {
	global $DB;

	$gateway = new OmGateway(getOmConfig());
	if (!$gateway->login()) {
		echo "Could not login User to OpenMeetings, check your OpenMeetings Module Configuration";
		exit();
	}
	$meeting->id = $DB->insert_record("openmeetings", $meeting);
	return updateOmRoom($meeting, $gateway);
}

function openmeetings_update_instance(&$meeting) {
	$gateway = new OmGateway(getOmConfig());
	if (!$gateway->login()) {
		echo "Could not login User to OpenMeetings, check your OpenMeetings Module Configuration";
		exit();
	}
	$meeting->timemodified = time();
	$meeting->id = $meeting->instance;
	return updateOmRoom($meeting, $gateway);
}

function updateOmRoom(&$meeting, $gateway) {
	global $DB, $mform;
	if ($meeting->type == 'recording') {
		$meeting->room_id = 0;
	} else {
		$room = getRoom($meeting);
		foreach ($meeting->remove as $mFileId => $selected) {
			if ($selected == 0) {
				unset($meeting->remove[$mFileId]);
			}
		}
		if (!empty($meeting->remove)) {
			$delIds = join(',', $meeting->remove);
			$DB->delete_records_select('openmeetings_file', 'id IN (' . $delIds . ')');
		}
		foreach ($DB->get_records('openmeetings_file', array('openmeetings_id' => $meeting->id)) as $mFile) {
			$room['files'][] = array('wbIdx' => $mFile->wb, 'fileId' => $mFile->file_id);
		}
		for ($i = 0; $i < $meeting->room_files; ++$i) {
			$wbIdx = $meeting->wb_idx[$i];
			$omFileId = $meeting->om_files[$i];
			$fileObj = new stdClass();
			$fileObj->openmeetings_id = $meeting->id;
			$fileObj->wb = $wbIdx;
			if ($omFileId > 0) {
				$fileObj->file_name = $meeting->{'om_int_file' . $omFileId};
				$fileObj->file_id = $omFileId;
				$fileObj->id = $DB->insert_record("openmeetings_file", $fileObj);
				$room['files'][] = array('wbIdx' => $wbIdx, 'fileId' => $omFileId);
				continue;
			}
			$file = $mform->getFile($i);
			if (!!$file) {
				$fileName = $file->get_filename();
				$fileObj->file_name = $fileName;
				$fileObj->file_id = 0;
				$fileObj->id = $DB->insert_record("openmeetings_file", $fileObj);
				$fileJson = array(
					'externalId' => $fileObj->id
					, 'name' => $fileName
				);
				$fileContent = $file->get_content();
				$omFile = $gateway->createFile($fileJson, $fileContent);
				if (!$omFile) {
					$DB->delete_records("openmeetings_file", array("id" => $fileObj->id));
				} else {
					$fileObj->file_id = $omFile['id'];
					$DB->update_record("openmeetings_file", $fileObj);
					$room['files'][] = array('wbIdx' => $wbIdx, 'fileId' => $omFile['id']);
				}
			}
		}
		$meeting->room_id = $gateway->updateRoom($room);
	}
	$DB->update_record("openmeetings", $meeting); // need to update room_id
	return $meeting->id;
}

function openmeetings_delete_instance($id) {
	global $DB;

	if (!$meeting = $DB->get_record("openmeetings", array("id" => "$id"))) {
		return false;
	}

	$result = true;

	$gateway = new OmGateway(getOmConfig());
	if (!$gateway->login()) {
		echo "Could not login User to OpenMeetings, check your OpenMeetings Module Configuration";
		exit();
	}
	if ($meeting->type != 'recording') {
		$meeting->room_id = $gateway->deleteRoom($meeting->room_id);
	}
	// processing room files
	$DB->delete_records("openmeetings_file", array("openmeetings_id" => $meeting->id));
	// delete room instance
	if (!$DB->delete_records("openmeetings", array("id" => $meeting->id))) {
		$result = false;
	}
	return $result;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param object $coursemodule
 * @return object info
 */
function openmeetings_get_coursemodule_info($coursemodule) {
	global $DB;

	if (!$meeting = $DB->get_record('openmeetings', array ('id' => $coursemodule->instance))) {
		return NULL;
	}

	if ($meeting->whole_window != 2) {
		return null;
	}
	$info = new cached_cm_info();
	$info->name = $meeting->name;
	$info->onclick = "window.open('" . new moodle_url('/mod/openmeetings/view.php', array ('id' => $coursemodule->id)) . "');return false;";
	return $info;
}

function openmeetings_user_outline() {
	return true;
}

function openmeetings_user_complete() {
	return true;
}

function openmeetings_print_recent_activity() {
	return false;  //  True if anything was printed, otherwise false
}

function openmeetings_cron() {
	return true;
}

function openmeetings_grades() {
	return NULL;
}

function openmeetings_get_participants() {
	return false;
}

function openmeetings_scale_used() {
	return false;
}

function openmeetings_scale_used_anywhere() {
	return false;
}

// Enables grading using Moodle's Activity completion API

function openmeetings_supports($feature) {
	switch($feature) {
		case FEATURE_GRADE_HAS_GRADE:
			return true;
		case FEATURE_BACKUP_MOODLE2:
			return true;
		default:
			return null;
	}
}
