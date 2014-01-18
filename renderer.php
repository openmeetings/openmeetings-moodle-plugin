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

class openmeetings implements renderable {
	var $om;
	
	public function __construct(stdclass $openmeetings) {
		$this->om = $openmeetings;
	}
}

class mod_openmeetings_renderer extends plugin_renderer_base {
	public function header() {
		// designed to be empty
	}
	
	public function footer() {
		// designed to be empty
	}
	
	private function _header(openmeetings $openmeetings) {
		global $cm, $course, $CFG, $USER, $PAGE;
		
		$title = $course->shortname . ": " . $openmeetings->om->name;
		$PAGE->set_title($title);
		$PAGE->set_cacheable(true);
		$PAGE->set_focuscontrol("");
		$PAGE->set_url('/mod/openmeetings/view.php', array(
				'id' => $cm->id
		));
		
		if ($openmeetings->om->whole_window == 1) {
			$out .= "<html" . $this->output->htmlattributes() . ">";
			$out .= html_writer::start_tag("head");
			$out .= html_writer::tag("title", $title);
			$out .= $this->output->standard_head_html();
			$out .= html_writer::end_tag("head");
			$out .= html_writer::start_tag("body", array("class" => "noMargin"));
		} else {
			// / Print the page header
			if ($course->category) {
				$navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
			} else {
				$navigation = '';
			}
			
			$stropenmeetingss = get_string("modulenameplural", "openmeetings");
			$stropenmeetings = get_string("modulename", "openmeetings");
			
			$PAGE->set_heading($course->fullname); // Required
			$PAGE->set_button(update_module_button($cm->id, $course->id, $stropenmeetings));
			$PAGE->navbar->add($stropenmeetingss, null, null, navigation_node::TYPE_CUSTOM, new moodle_url($CFG->wwwroot . '/user/index.php?id=' . $courseid));
			$PAGE->navbar->add($openmeetings->om->name);
			
			$out .= $this->output->header();
		}
		return $out;
	}

	private function _footer(openmeetings $openmeetings) {
		if ($openmeetings->om->whole_window == 1) {
			$out .= html_writer::end_tag("body");
			$out .= html_writer::end_tag("html");
		} else {
			$out .= $this->output->footer();
		}
		return $out;
	}
	
	protected function render_openmeetings(openmeetings $openmeetings) {
		global $cm, $course, $CFG, $USER, $PAGE;

		$out .= $this->_header($openmeetings);
		$context = context_module::instance($cm->id);
		$becomemoderator = 0;
		if (has_capability('mod/openmeetings:becomemoderator', $context)) {
			$becomemoderator = 1;
		}
		
		$gateway = new openmeetings_gateway(getOmConfig());
		if ($gateway->loginuser()) {
			
			$allowRecording = 1;
			if ($openmeetings->om->allow_recording == 2) {
				$allowRecording = 0;
			}
			if ($openmeetings->om->is_moderated_room == 3) {
				$becomemoderator = 1;
			}
			
			$profilePictureUrl = moodle_url::make_pluginfile_url(context_user::instance($USER->id)->id, 'user', 'icon', NULL, '/', 'f2')->out(false);
			
			// Simulate the User automatically
			if ($openmeetings->om->type != 0) {
				$returnVal = $gateway->setUserObjectAndGenerateRoomHashByURLAndRecFlag($USER->username, $USER->firstname, $USER->lastname
						, $profilePictureUrl, $USER->email, $USER->id, $CFG->openmeetings_openmeetingsModuleKey, $openmeetings->om->room_id
						, $becomemoderator, $allowRecording);
			} else {
				$returnVal = $gateway->setUserObjectAndGenerateRecordingHashByURL($USER->username, $USER->firstname, $USER->lastname
						, $USER->id, $CFG->openmeetings_openmeetingsModuleKey, $openmeetings->om->room_recording_id);
			}
			
			if ($returnVal != "") {
				$scope_room_id = $openmeetings->om->room_id;
				
				if ($scope_room_id == 0 || $openmeetings->om->type == 0) {
					$scope_room_id = "hibernate";
				}
				
				$url = "http://" . $CFG->openmeetings_red5host . ":" . $CFG->openmeetings_red5port . "/" . $CFG->openmeetings_webappname . "/swf?" 
						. "&secureHash=" . $returnVal 
						. "&scopeRoomId=" . $scope_room_id 
						. "&language=" . $openmeetings->om->language 
						. "&user_id=" . $USER->id 
						. "&moodleRoom=1" . "&wwwroot=" . $CFG->wwwroot;
				
				$height = $openmeetings->om->whole_window == 1 ? "100%" : "640px";
				$out .= html_writer::empty_tag("iframe", array(
						"src" => $url,
						"class" => "openmeetings" . ($openmeetings->om->whole_window == 1 ? " wholeWindow" : "")
				));
			}
		} else {
			echo "Could not login User to OpenMeetings, check your OpenMeetings Module Configuration";
			exit();
		}
		
		$out .= $this->_footer($openmeetings);
		return $out;
	}
}
