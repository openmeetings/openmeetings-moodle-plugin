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

require_once($CFG->dirroot . '/mod/openmeetings/lib.php');

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
		global $cm, $course, $CFG, $USER, $PAGE, $OUTPUT;

		$title = $course->shortname . ": " . $openmeetings->om->name;
		$PAGE->set_title($title);
		$PAGE->set_cacheable(false);
		$PAGE->set_focuscontrol("");
		$PAGE->set_url('/mod/openmeetings/view.php', array(
				'id' => $cm->id
		));

		if ($openmeetings->om->whole_window > 0) {
			$out .= "<html" . $this->output->htmlattributes() . ">";
			$out .= html_writer::start_tag("head");
			$out .= html_writer::empty_tag("meta", array(
					"http-equiv" => "pragma",
					"content" => "no-cache")
				);
			$out .= html_writer::empty_tag("meta", array(
					"http-equiv" => "expires",
					"content" => "-1")
				);
			$out .= html_writer::empty_tag("meta", array(
					"http-equiv" => "cache-control",
					"content" => "no-cache")
				);
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

			$PAGE->set_heading($course->fullname); // Required
			$PAGE->navbar->add($stropenmeetingss, null, null, navigation_node::TYPE_CUSTOM, new moodle_url($CFG->wwwroot . '/user/index.php?id=' . $courseid));
			$PAGE->navbar->add($openmeetings->om->name);

			$out .= $this->output->header();
		}
		return $out;
	}

	private function _footer(openmeetings $openmeetings) {
		if ($openmeetings->om->whole_window > 0) {
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
		$becomemoderator = false;
		if (has_capability('mod/openmeetings:becomemoderator', $context)) {
			$becomemoderator = true;
		}
		$gateway = new OmGateway(getOmConfig());
		if ($gateway->login()) {
			$allowRecording = $openmeetings->om->allow_recording != 2;
			if ($openmeetings->om->is_moderated_room == 3) {
				$becomemoderator = true;
			}
			// Simulate the User automatically
			if ($openmeetings->om->type != 'recording') {
				$hash = getOmHash($gateway, array("roomId" => $openmeetings->om->room_id, "moderator" => $becomemoderator, "allowRecording" => $allowRecording));
			} else {
				$hash = getOmHash($gateway, array("recordingId" => $openmeetings->om->room_recording_id));
			}

			if ($hash != "") {
				$url = $gateway->getUrl() . "/hash?&secure=" . $hash . "&language=" . $openmeetings->om->language;
				$height = $openmeetings->om->whole_window > 0 ? "100%" : "640px";
				$out .= html_writer::empty_tag("iframe", array(
						"src" => $url,
						"allow" => "microphone; camera",
						"class" => "openmeetings" . ($openmeetings->om->whole_window > 0 ? " wholeWindow" : "")
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
