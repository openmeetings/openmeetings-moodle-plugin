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

require_once ("../../config.php");
require_once ("lib.php");

$id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
$g = optional_param('g', 0, PARAM_INT);

if (!empty($id)) {
	if (!$cm = get_coursemodule_from_id('openmeetings', $id)) {
		print_error('invalidcoursemodule');
	}
	if (!$course = $DB->get_record("course", array(
			"id" => $cm->course 
	))) {
		print_error('coursemisconf');
	}
	if (!$openmeetings = $DB->get_record("openmeetings", array(
			"id" => $cm->instance 
	))) {
		print_error('invalidid', 'openmeetings');
	}
} else if (!empty($g)) {
	if (!$openmeetings = $DB->get_record("openmeetings", array(
			"id" => $g 
	))) {
		print_error('invalidid', 'openmeetings');
	}
	if (!$course = $DB->get_record("course", array(
			"id" => $openmeetings->course 
	))) {
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

add_to_log($course->id, "openmeetings", "view", "view.php?id=$cm->id", "$openmeetings->id");

$output = $PAGE->get_renderer('mod_openmeetings');
$openmeetingswidget = new openmeetings($openmeetings, false);

echo $output->header();
echo $output->render($openmeetingswidget);
echo $output->footer();
?>
