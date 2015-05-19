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

if (!defined('MOODLE_INTERNAL')) {
	die('Direct access to this script is forbidden.'); // It must be included from a Moodle page
}

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/openmeetings/lib.php');

$config = array(
		"protocol" => "http",
		"port" => $CFG->openmeetings_red5port,
		"host" => $CFG->openmeetings_red5host,
		"webappname" => $CFG->openmeetings_webappname,
		"adminUser" => $CFG->openmeetings_openmeetingsAdminUser,
		"adminPass" => $CFG->openmeetings_openmeetingsAdminUserPass,
		"moduleKey" => $CFG->openmeetings_openmeetingsModuleKey
);
$gateway = new openmeetings_gateway(getOmConfig());
$om_login = $gateway->loginuser();
class mod_openmeetings_mod_form extends moodleform_mod {
	function definition() {
		global $gateway, $om_login;
		$mform = $this->_form;

		// -------------------------------------------------------------------------------
		// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'general', get_string('general', 'form'));
		// / Adding the standard "name" field
		$mform->addElement('text', 'name', get_string('Room_Name', 'openmeetings'), array(
				'size' => '64'
		));
		// $mform->setType('name', PARAM_TEXT);
		$mform->addRule('name', null, 'required', null, 'client');
		$mform->setType('name', PARAM_TEXT);
	
		$mform->addElement('hidden', 'room_id', '0', array(
				'size' => '64'
		));
		$mform->setType('room_id', PARAM_INT);

		// Adding the "Room Type" field
		$mform->addElement('select', 'type', get_string('Room_Type', 'openmeetings'), array(
				'1' => get_string('Conference', 'openmeetings'),
				'3' => get_string('Restricted', 'openmeetings'),
				'4' => get_string('Interview', 'openmeetings'),
				'0' => get_string('Recording', 'openmeetings')
		));

		// Adding the "Number of Participants" field
		$mform->addElement('select', 'max_user', get_string('Max_User', 'openmeetings'), array(
				'2' => '2',
				'4' => '4',
				'8' => '8',
				'16' => '16',
				'24' => '24',
				'36' => '36',
				'50' => '50',
				'100' => '100',
				'150' => '150',
		));

		// Adding the "Room Language" field
		$language_array = array(
				'1' => 'english',
				'2' => 'deutsch',
				'3' => 'deutsch (studIP)',
				'4' => 'french',
				'5' => 'italian',
				'6' => 'portugues',
				'7' => 'portugues brazil',
				'8' => 'spanish',
				'9' => 'russian',
				'10' => 'swedish',
				'11' => 'chinese simplified',
				'12' => 'chinese traditional',
				'13' => 'korean',
				'14' => 'arabic',
				'15' => 'japanese',
				'16' => 'indonesian',
				'17' => 'hungarian',
				'18' => 'turkish',
				'19' => 'ukrainian',
				'20' => 'thai',
				'21' => 'persian',
				'22' => 'czech',
				'23' => 'galician',
				'24' => 'finnish',
				'25' => 'polish',
				'26' => 'greek',
				'27' => 'dutch',
				'28' => 'hebrew',
				'29' => 'catalan',
				'30' => 'bulgarian',
				'31' => 'danish',
				'32' => 'slovak'
		);

		$mform->addElement('select', 'language', get_string('Room_Language', 'openmeetings'), $language_array);

		// Some description
		$mform->addElement('static', 'description', '', get_string('Moderation_Description', 'openmeetings'));

		// Adding the "Is Moderated Room" field
		$mform->addElement('select', 'is_moderated_room', get_string('Wait_for_teacher', 'openmeetings'), array(
				'1' => get_string('Moderation_TYPE_1', 'openmeetings'),
				'2' => get_string('Moderation_TYPE_2', 'openmeetings'),
				'3' => get_string('Moderation_TYPE_3', 'openmeetings')
		));

		$mform->addElement('select', 'allow_recording', get_string('Allow_Recording', 'openmeetings'), array(
				'1' => get_string('Recording_TYPE_1', 'openmeetings'),
				'2' => get_string('Recording_TYPE_2', 'openmeetings')
		));

		$mform->addElement('select', 'whole_window', get_string('whole_window', 'openmeetings'), array(
				'0' => get_string('whole_window_type_1', 'openmeetings'),
				'1' => get_string('whole_window_type_2', 'openmeetings'),
				'2' => get_string('whole_window_type_3', 'openmeetings')
		));

		// Adding the optional "intro" field
		$this->standard_intro_elements(get_string('description', 'openmeetings'));

		// Adding the "Available Recordings to Shows" field
		$recordings = array();

		if ($om_login) {
			$flvrecordings = $gateway->getRecordingsByExternalRooms();

			foreach ($flvrecordings as $rec) {
				$recId = $rec['flvRecordingId'] ? $rec['flvRecordingId'] : $rec['id'];
				$recName = $rec['fileName'] ? $rec['fileName'] : $rec['name'];
				if ($recId) {
					$recordings[$recId] = $recName;
				}
			}
		}

		$mform->registerNoSubmitButton('avi');
		$mform->registerNoSubmitButton('flv');
		$dgrp = array();
		$dgrp[] = & $mform->createElement('static', 'description', '', get_string('recordings_label', 'openmeetings'));
		$dgrp[] = & $mform->createElement('select', 'room_recording_id', get_string('recordings_show', 'openmeetings'), $recordings);
		$dgrp[] = & $mform->createElement('submit', 'avi', get_string('download_avi', 'openmeetings'));
		$dgrp[] = & $mform->createElement('submit', 'flv', get_string('download_flv', 'openmeetings'));
		$mform->addGroup($dgrp, 'dgrp', get_string('recordings_show', 'openmeetings'), array(' '), false);
				$mform->setType('avi', PARAM_NOTAGS);
		$mform->setType('flv', PARAM_NOTAGS);

		// -------------------------------------------------------------------------------
		// add standard elements, common to all modules
		$this->standard_coursemodule_elements();

		// -------------------------------------------------------------------------------
		// add standard buttons, common to all modules
		$this->add_action_buttons();
	}
}

global $data, $cm, $CFG, $USER;
$course = $DB->get_record('course', array(
		'id' => $data->course
), '*', MUST_EXIST);
$mform = new mod_openmeetings_mod_form($data, $data->section, $cm, $course);

if ($mform->no_submit_button_pressed() && $om_login) {
	$recId = $mform->get_submitted_data()->{'room_recording_id'};
	$type = isset($mform->get_submitted_data()->{'avi'}) ? "avi" : (isset($mform->get_submitted_data()->{'flv'}) ? "flv" : "none");
	$filename = "flvRecording_$recId.$type";
	if ($om_login) {
		header('Content-disposition: attachment; filename=' . $filename);
		header('Content-type: video/' . $type);
		ob_clean();
		$url = $gateway->getUrl() . "/recordings/$type/" . getRecordingHash($gateway, $recId);
		readfile($url);
	}
	exit(0);
}

