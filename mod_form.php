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

/**
 * @package mod_openmeetings
 **/

global $data, $cm, $CFG;

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/openmeetings/lib.php');
require_once($CFG->dirroot . '/mod/openmeetings/version.php');

$gateway = new OmGateway(getOmConfig());
$om_login = $gateway->login();
class mod_openmeetings_mod_form extends moodleform_mod {
	private function getLanguages() {
		return array(
				'1' => 'english',
				'2' => 'deutsch',
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
	}

	private function addGeneralFields() {
		$mform = $this->_form;
		// Adding the standard "name" field
		$mform->addElement('text', 'name', get_string('Room_Name', 'openmeetings'), array(
				'size' => '64'
		));
		// $mform->setType('name', PARAM_TEXT);
		$mform->addRule('name', null, 'required', null, 'client');
		$mform->setType('name', PARAM_TEXT);

		$mform->addElement('hidden', 'room_id', '0', array('size' => '64'));
		$mform->setType('room_id', PARAM_INT);

		// Adding the "Room Type" field
		$mform->addElement('select', 'type', get_string('Room_Type', 'openmeetings'), array(
				'CONFERENCE' => get_string('Conference', 'openmeetings'),
				'PRESENTATION' => get_string('Restricted', 'openmeetings'),
				'INTERVIEW' => get_string('Interview', 'openmeetings'),
				'recording' => get_string('Recording', 'openmeetings')
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
		$mform->disabledIf('max_user', 'type', 'eq', 'recording');

		// Adding the "Room Language" field
		$mform->addElement('select', 'language', get_string('Room_Language', 'openmeetings'), $this->getLanguages());

		// Some description
		$mform->addElement('static', 'description', '', get_string('Moderation_Description', 'openmeetings'));

		// Adding the "Is Moderated Room" field
		$mform->addElement('select', 'is_moderated_room', get_string('Wait_for_teacher', 'openmeetings'), array(
				'1' => get_string('Moderation_TYPE_1', 'openmeetings'),
				'2' => get_string('Moderation_TYPE_2', 'openmeetings'),
				'3' => get_string('Moderation_TYPE_3', 'openmeetings')
		));
		$mform->disabledIf('is_moderated_room', 'type', 'eq', 'recording');

		$mform->addElement('select', 'allow_recording', get_string('Allow_Recording', 'openmeetings'), array(
				'1' => get_string('Recording_TYPE_1', 'openmeetings'),
				'2' => get_string('Recording_TYPE_2', 'openmeetings')
		));
		$mform->disabledIf('allow_recording', 'type', 'eq', 'recording');

		$mform->addElement('select', 'chat_hidden', get_string('Chat_Hidden', 'openmeetings'), array(
				'0' => get_string('Chat_Hidden_TYPE_1', 'openmeetings'),
				'1' => get_string('Chat_Hidden_TYPE_2', 'openmeetings')
		));
		$mform->disabledIf('chat_hidden', 'type', 'eq', 'recording');

		$mform->addElement('select', 'whole_window', get_string('whole_window', 'openmeetings'), array(
				'0' => get_string('whole_window_type_1', 'openmeetings'),
				'1' => get_string('whole_window_type_2', 'openmeetings'),
				'2' => get_string('whole_window_type_3', 'openmeetings')
		));

		// Adding the optional "intro" field
		$this->standard_intro_elements(get_string('description', 'openmeetings'));
	}

	private function addRecordings($recordings) {
		$mform = $this->_form;
		// Adding the "Available Recordings to Show" field
		$mform->registerNoSubmitButton('mp4');
		$dwnld_grp = array();
		$dwnld_grp[] = & $mform->createElement('html', '<div class="col-md-12">');
		$dwnld_grp[] = & $mform->createElement('static', 'description', '', get_string('recordings_label', 'openmeetings'));
		$dwnld_grp[] = & $mform->createElement('html', '</div>');
		$dwnld_grp[] = & $mform->createElement('html', '<div class="om-labeled-group col-md-12">');
		$dwnld_grp[] = & $mform->createElement('select', 'room_recording_id', get_string('recordings_show', 'openmeetings'), $recordings, array('class' => 'inline col-md-8'));
		$dwnld_grp[] = & $mform->createElement('submit', 'mp4', get_string('download_mp4', 'openmeetings'), array('class' => 'inline col-md-3'));
		$dwnld_grp[] = & $mform->createElement('html', '</div>');
		$mform->disabledIf('mp4', 'room_recording_id', 'lt', '1');
		$mform->addGroup($dwnld_grp, 'dwnld_grp', get_string('recordings_show', 'openmeetings'), array(' '), false);
		$mform->disabledIf('dwnld_grp', 'type', 'neq', 'recording');
	}

	private function addFiles($files) {
		$mform = $this->_form;
		foreach ($files as $fileId => $fileName) {
			$hname = 'om_int_file' . $fileId;
			$mform->addElement('hidden', $hname, $fileName);
			$mform->setType($hname, PARAM_TEXT);
		}
		$mform->addElement('html', '<div class="om-labeled-group">');

		foreach ($this->current->files as $cFile) {
			$curId = 'curfile_grp' . $cFile->id;
			$curfile_grp = array();
			$curfile_grp[] = & $mform->createElement('html', '<div class="om-labeled-group col-md-12">');
			$curfile_grp[] = & $mform->createElement('html', '<div class="inline col-md-3">');
			$curfile_grp[] = & $mform->createElement('static', 'file', '', $cFile->file_name);
			$curfile_grp[] = & $mform->createElement('html', '</div>');
			$curfile_grp[] = & $mform->createElement('html', '<div class="inline col-md-3">');
			$curfile_grp[] = & $mform->createElement('static', 'wb_idx_lbl', '', get_string('wb_index', 'openmeetings'));
			$curfile_grp[] = & $mform->createElement('html', '</div>');
			$curfile_grp[] = & $mform->createElement('html', '<div class="inline col-md-3">');
			$curfile_grp[] = & $mform->createElement('static', 'wb', '', $cFile->wb);
			$curfile_grp[] = & $mform->createElement('html', '</div>');
			$curfile_grp[] = & $mform->createElement('advcheckbox', 'remove[' . $cFile->id . ']', get_string('remove', 'openmeetings'), '', array('group' => 1, 'class' => 'inline col-md-3'), array(0, $cFile->id));
			$curfile_grp[] = & $mform->createElement('html', '</div>');
			$mform->addGroup($curfile_grp, $curId, get_string('room_file', 'openmeetings'), array(' '), false);
			$mform->disabledIf($curId, 'type', 'eq', 'recording');
		}

		$rep_newfile_grp = array();
		$rep_newfile_grp[] = & $mform->createElement('select', 'wb_idx', get_string('wb_index', 'openmeetings'), array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10));
		$rep_newfile_grp[] = & $mform->createElement('select', 'om_files', get_string('om_file', 'openmeetings'), $files);
		$rep_newfile_grp[] = & $mform->createElement('filepicker', 'userfile', get_string('file'), null,
				array('accepted_types' => '*'));
		$rep_newfile = array();
		$rep_newfile[] = $mform->createElement('group', 'file_grp', get_string('room_file', 'openmeetings'), $rep_newfile_grp, ' ', false);

		$rep_options = array();
		$rep_options['wb_idx']['disabledif'] = array('type', 'eq', 'recording');
		$rep_options['om_files']['disabledif'] = array('type', 'eq', 'recording');

		$this->repeat_elements($rep_newfile, 1, $rep_options, 'room_files', 'add_room_files', 1, null, true);

		$mform->addElement('html', '</div>');
	}

	private function fillFiles(&$recordings, &$files) {
		global $gateway, $om_login;

		if ($om_login) {
			$omrecordings = $gateway->getRecordings();
			foreach ($omrecordings as $rec) {
				$recId = $rec['id'];
				$recName = $rec['name'];
				if ($recId) {
					$recordings[$recId] = $recName;
				}
			}

			$omfiles = $gateway->getFiles();
			foreach ($omfiles as $file) {
				$fileId = $file['id'];
				$fileName = $file['name'];
				if ($fileId) {
					$files[$fileId] = $fileName;
				}
			}
		}
	}

	function definition() {
		global $gateway, $plugin;
		$mform = $this->_form;

		// -------------------------------------------------------------------------------
		// Adding the "general" fieldset, where all the common settings are showed
		$mform->addElement('header', 'general', get_string('general', 'form'));
		if ($plugin->om_check) {
			$min = preg_split('/[.-]/', $plugin->om_version);
			$cur = preg_split('/[.-]/', $gateway->version()["version"]);
			$ok = $cur[0] < $min[0] || $cur[1] < $min[1] || $cur[2] < $min[2];
			if ($ok) {
				$msg = get_string('Version_Ok', 'openmeetings') . $gateway->version()["version"];
			} else {
				$msg = get_string('Version_Bad', 'openmeetings') . $plugin->om_version;
			}
			$mform->addElement('html', '<div class="' . ($ok ? 'green' : 'red') . '">' . $msg . '</div>');
		}
		$recordings = array();
		$files = array(-1 => get_string('upload_file', 'openmeetings'));
		$this->fillFiles($recordings, $files);

		$this->addGeneralFields();
		$this->addRecordings($recordings);
		if ($this->current->room_id > 0) {
			$mform->registerNoSubmitButton('cleanWb');
			$mform->addElement('submit', 'cleanWb', get_string('clean_wb', 'openmeetings'));
		}

		// room files
		$mform->addElement('header', 'room_files_header', get_string('room_files', 'openmeetings'));
		$this->addFiles($files);

		// -------------------------------------------------------------------------------
		// add standard elements, common to all modules
		$this->standard_coursemodule_elements();

		// -------------------------------------------------------------------------------
		// add standard buttons, common to all modules
		$this->add_action_buttons();
	}

	function getFile($idx = 0) {
		global $USER;
		$grp = $this->_form->getElement('file_grp[' . $idx . ']');
		if ($grp instanceof MoodleQuickForm_group) {
			$picker = $grp->getElements()[2];
			if ($picker instanceof MoodleQuickForm_filepicker) {
				$fs = get_file_storage();
				$context = context_user::instance($USER->id);
				$files = $fs->get_area_files($context->id, 'user', 'draft', $picker->getValue(), 'id DESC', false);
				if ($files) {
					return reset($files);
				}
			}
		}
		return false;
	}
}

$course = $DB->get_record('course', array('id' => $data->course), '*', MUST_EXIST);
if ($data->id > 0) {
	$data->files = $DB->get_records('openmeetings_file', array('openmeetings_id' => $data->id));
}
$mform = new mod_openmeetings_mod_form($data, $data->section, $cm, $course);

$sdata = $mform->get_submitted_data();
if ($mform->no_submit_button_pressed() && $om_login) {
	if ($sdata->{'mp4'}) {
		$recId = $sdata->{'room_recording_id'};
		$type = "mp4";
		$filename = "flvRecording_$recId.$type";
		if ($om_login) {
			ob_end_clean();
			if (ini_get_bool('zlib.output_compression')) {
				ini_set('zlib.output_compression', 'Off');
			}
			header('Content-disposition: attachment; filename=' . $filename);
			header('Content-type: video/' . $type);
			$url = $gateway->getUrl() . "/recordings/$type/" . getOmHash($gateway, array("recordingId" => $recId));
			readfile($url);
		}
		exit(0);
	} else if ($sdata->{'cleanWb'} && $data->room_id > 0) {
		$gateway->cleanWb($data->room_id);
	}
}
