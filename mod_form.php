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

if (!defined('MOODLE_INTERNAL')) {
	die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once ($CFG->dirroot.'/course/moodleform_mod.php');

$openmeetings_gateway = new openmeetings_gateway();
$om_login = $openmeetings_gateway->openmeetings_loginuser();

class mod_openmeetings_mod_form extends moodleform_mod {

	function definition() {

		global $openmeetings_gateway, $om_login;
		$mform = $this->_form;

		//-------------------------------------------------------------------------------
		/// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'general', get_string('general', 'form'));
		/// Adding the standard "name" field
		$mform->addElement('text', 'name', get_string('Room_Name', 'openmeetings'), array('size'=>'64'));
		//$mform->setType('name', PARAM_TEXT);
		$mform->addRule('name', null, 'required', null, 'client');

		$mform->addElement('hidden', 'room_id', '0', array('size'=>'64'));

		/// Adding the "Room Type" field
		$mform->addElement('select', 'type', get_string('Room_Type', 'openmeetings'), array('1'=>get_string('Conference', 'openmeetings'), '3'=>get_string('Restricted', 'openmeetings'), '4'=>get_string('Interview', 'openmeetings'), '0'=>get_string('Recording', 'openmeetings')));

		/// Adding the "Number of Participants" field
		$mform->addElement('select', 'max_user', get_string('Max_User', 'openmeetings'), array('2'=>'2', '4'=>'4', '8'=>'8', '16'=>'16', '24'=>'24', '36'=>'36', '50'=>'50', '100'=>'100', '150'=>'150', '200'=>'200', '250'=>'250', '500'=>'500', '1000'=>'1000'));

		/// Adding the "Room Language" field
		$language_array = array ('1' => 'english',
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
											'32' => 'slovak');
			
		$mform->addElement('select', 'language', get_string('Room_Language', 'openmeetings'), $language_array);
		
		/// Some description
		$mform->addElement('static', 'description', '', get_string('Moderation_Description', 'openmeetings'));
		
		/// Adding the "Is Moderated Room" field
		$mform->addElement('select', 'is_moderated_room', get_string('Wait_for_teacher', 'openmeetings'), array('1'=> get_string('Moderation_TYPE_1', 'openmeetings'),'2' => get_string('Moderation_TYPE_2', 'openmeetings'),'3' => get_string('Moderation_TYPE_3', 'openmeetings')));

		$mform->addElement('select', 'allow_recording', get_string('Allow_Recording', 'openmeetings'), array('1'=> get_string('Recording_TYPE_1', 'openmeetings'),'2' => get_string('Recording_TYPE_2', 'openmeetings')));
			
			
		/// Adding the optional "intro" and "introformat" pair of fields
		$mform->addElement('htmleditor', 'intro', get_string('Comment', 'openmeetings'));
		$mform->setType('intro', PARAM_RAW);
		
		
		/// Adding the "Available Recordings to Shows" field
		$recordings = array();
			
		if ($om_login) {
		
			$resultDom = $openmeetings_gateway->openmeetings_getRecordingsByExternalRooms();
		
			$flvrecordings = $resultDom->getElementsByTagName('return');
				
			foreach ($flvrecordings as $flvrecording) {
				
				$flvRecordingIdNode = $flvrecording->getElementsByTagName('flvRecordingId');
				$flvRecordingNameNode = $flvrecording->getElementsByTagName('fileName');
				if ($flvRecordingIdNode->length > 0) {
					$recordings[$flvRecordingIdNode->item(0)->nodeValue] = $flvRecordingNameNode->item(0)->nodeValue;
				}
				
			}
				
		
		
		}
		
		//$mform->registerNoSubmitButton('download_rec');
		$mform->registerNoSubmitButton('avi');
		$mform->registerNoSubmitButton('flv');
		$dgrp = array();
		$dgrp[] =& $mform->createElement('static', 'description', '', get_string('recordings_label', 'openmeetings'));
		$dgrp[] =& $mform->createElement('select', 'room_recording_id', get_string('recordings_show', 'openmeetings'), $recordings);
		$dgrp[] =& $mform->createElement('submit', 'avi', get_string('download_avi', 'openmeetings'));
		$dgrp[] =& $mform->createElement('submit', 'flv', get_string('download_flv', 'openmeetings'));
		$mform->addGroup($dgrp, 'dgrp', get_string('recordings_show', 'openmeetings'), array(' '), false);
		//$mform->setType('download_rec', PARAM_NOTAGS);
		$mform->setType('avi', PARAM_NOTAGS);
		$mform->setType('flv', PARAM_NOTAGS);
		
		//$mform->addRule('intro', get_string('required'), 'required', null, 'client');
		//$mform->setHelpButton('intro', array('writing', 'richtext'), false, 'editorhelpbutton');

		//$mform->addElement('format', 'introformat', get_string('format', 'openmeetings'));
		//$this->add_intro_editor(true, get_string('description', 'mplayer'));

		//$this->add_intro_editor(true);

		//-------------------------------------------------------------------------------
		// add standard elements, common to all modules
		//$this->standard_coursemodule_elements(array('groups'=>true, 'groupings'=>true, 'groupmembersonly'=>true));
		$this->standard_coursemodule_elements();

		//-------------------------------------------------------------------------------
		// add standard buttons, common to all modules
		$this->add_action_buttons();

	}
}

global $data, $cm;
$course = $DB->get_record('course', array('id'=>$data->course), '*', MUST_EXIST);
$mform = new mod_openmeetings_mod_form($data, $data->section, $cm, $course);

if ($mform->no_submit_button_pressed() && $om_login) {
	$type = isset($mform->get_submitted_data()->{'avi'}) ? "avi" :
			(isset($mform->get_submitted_data()->{'flv'}) ? "flv" : "none");
	$filename = 'flvRecording_' . $mform->get_submitted_data()->{'room_recording_id'} . '.' . $type;
	header('Content-disposition: attachment; filename=' . $filename);
	header('Content-type: video/' . $type);
	readfile($openmeetings_gateway->getUrl() . '/DownloadHandler?fileName=' . $filename
                . '&moduleName=lzRecorderApp&parentPath=&room_id='
                . '&sid=' . $openmeetings_gateway->session_id);
	exit(0);
}

?>
