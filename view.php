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


require_once("../../config.php");
require_once("lib.php");


$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$g  = optional_param('g', 0, PARAM_INT);

if (!empty($id)) {
	if (! $cm = get_coursemodule_from_id('openmeetings', $id)) {
		print_error('invalidcoursemodule');
	}
	if (! $course = $DB->get_record("course", array("id"=>$cm->course))) {
		print_error('coursemisconf');
	}
	if (! $openmeetings = $DB->get_record("openmeetings", array("id"=>$cm->instance))) {
		print_error('invalidid', 'openmeetings');
	}

} else if (!empty($g)) {
	if (! $openmeetings = $DB->get_record("openmeetings", array("id"=>$g))) {
		print_error('invalidid', 'openmeetings');
	}
	if (! $course = $DB->get_record("course", array("id"=>$openmeetings->course))) {
		print_error('invalidcourseid');
	}
	if (!$cm = get_coursemodule_from_instance("openmeetings", $openmeetings->id, $course->id)) {
		print_error('invalidcoursemodule');
	}
	$id = $cm->id;
} else {
	print_error('invalidid', 'openmeetings');
}


require_login($course->id);
$PAGE->set_url('/mod/openmeetings/view.php', array('id' => $cm->id));

add_to_log($course->id, "openmeetings", "view", "view.php?id=$cm->id", "$openmeetings->id");

/// Print the page header

if ($course->category) {
	$navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
} else {
	$navigation = '';
}

$stropenmeetingss = get_string("modulenameplural", "openmeetings");
$stropenmeetings  = get_string("modulename", "openmeetings");

$PAGE->set_heading($course->fullname); // Required
$PAGE->set_title($course->shortname . ": " . $openmeetings->name);
$PAGE->set_cacheable(true);
$PAGE->set_focuscontrol("");
$PAGE->set_button(update_module_button($cm->id, $course->id, $stropenmeetings));
$PAGE->navbar->add($stropenmeetingss, null, null, navigation_node::TYPE_CUSTOM, new moodle_url($CFG->wwwroot.'/user/index.php?id='.$courseid));
$PAGE->navbar->add($openmeetings->name);
 
echo $OUTPUT->header();

$colors = Array ("FFFF00", "CCCC00", "FFCC00", "CC9933", "996600", "FF9900", "CC9966", "CC6600", "996633", "663300", "FF6600", "CC6633", "993300", "660000", "FF6633", "CC3300", "FF3300", "FF0000", "CC0000", "990000", "FF3333", "FF0033", "CC0033", "CC6666", "CC3333", "993333", "990033", "330000", "FF3366", "FF0066", "CC3366", "996666", "663333", "9966CC", "9966FF", "6600CC", "6633CC", "663399", "330033", "3333FF", "3300FF", "3300CC", "3333CC", "000099", "000066", "99CCCC", "66CCCC", "339999", "669999", "006666", "336666", "66CC66", "669966", "336633", "003300", "006600", "CCCC66", "CCCC33", "999966", "999933", "999900", "666600");

$colorid = rand (0, 61);

$sitelink = str_replace("http://", "", $CFG->wwwroot);

$context = context_module::instance($cm->id);
 
$becomemoderator = 0;
if (has_capability('mod/openmeetings:becomemoderator', $context)) {
	$becomemoderator = 1;
	//echo "BECOME MODERATOR IS TRUE<br/>";
}


$openmeetings_gateway = new openmeetings_gateway(getOmConfig());
if ($openmeetings_gateway->loginuser()) {
		
	$allowRecording = 1;
	if ($openmeetings->allow_recording == 2) {
		$allowRecording = 0;
	}
	if ($openmeetings->is_moderated_room == 3) {
		$becomemoderator = 1;
	}
		
	$profilePictureUrl = moodle_url::make_pluginfile_url(
		context_user::instance($USER->id)->id, 'user', 'icon', NULL, '/', 'f2')->out(false);

	// Simulate the User automatically
	if ($openmeetings->type != 0){
		$returnVal = $openmeetings_gateway->setUserObjectAndGenerateRoomHashByURLAndRecFlag($USER->username,$USER->firstname,
		$USER->lastname,$profilePictureUrl,$USER->email,$USER->id,$CFG->openmeetings_openmeetingsModuleKey,$openmeetings->room_id,$becomemoderator,$allowRecording);
	} else {
		$returnVal = $openmeetings_gateway->setUserObjectAndGenerateRecordingHashByURL($USER->username,$USER->firstname,
		$USER->lastname,$USER->id,$CFG->openmeetings_openmeetingsModuleKey,$openmeetings->room_recording_id);
	}
		
	if ($returnVal != "") {

		$scope_room_id = $openmeetings->room_id;

		if ($scope_room_id == 0 || $openmeetings->type == 0) {
			$scope_room_id = "hibernate";
		}

		$iframe_d = "http://".$CFG->openmeetings_red5host . ":" . $CFG->openmeetings_red5port .
							 	"/".$CFG->openmeetings_webappname."/?" .
								"secureHash=" . $returnVal .
								"&scopeRoomId=" . $scope_room_id .
								"&language=" . $openmeetings->language .
								"&user_id=". $USER->id .
								"&moodleRoom=1" .
								"&wwwroot=". $CFG->wwwroot;

		printf("<iframe src='%s' width='%s' height='%s' />",$iframe_d,"100%",640);
	}
} else {
	echo "Could not login User to OpenMeetings, check your OpenMeetings Module Configuration";
	exit();
}


/// Finish the page
echo $OUTPUT->footer();

