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
/**
 * This page lists all the instances of openmeetings in a particular course
 *
 * @author Sebastian Wagner
 * @version 1.7
 * @package openmeetings
 **/

// Replace openmeetings with the name of your module
require_once("../../config.php");
require_once("lib.php");

$id = required_param('id', PARAM_INT);   // Course

if (! $course = get_record("course", "id", $id)) {
	error("Course ID is incorrect");
}

require_login($course->id);
$event = \mod_openmeetings\event\course_module_instance_list_viewed::create(array(
    'context' => context_course::instance($course->id)
));
$event->trigger();

// Get all required stringsopenmeetings
$stropenmeetings = get_string("modulenameplural", "openmeetings");
$stropenmeetings  = get_string("modulename", "openmeetings");

// Print the header
if ($course->category) {
	$navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
} else {
	$navigation = '';
}

print_header("$course->shortname: $stropenmeetings", "$course->fullname", "$navigation $stropenmeetings", "", "", true, "", navmenu($course));

// Get all the appropriate data
if (! $openmeetings = get_all_instances_in_course("openmeetings", $course)) {
	notice("There are no openmeetings", "../../course/view.php?id=$course->id");
	die;
}

// Print the list of instances (your module will probably extend this)
$timenow = time();
$strname = get_string("name");
$strweek = get_string("week");
$strtopic = get_string("topic");

if ($course->format == "weeks") {
	$table->head  = array ($strweek, $strname);
	$table->align = array ("center", "left");
} else if ($course->format == "topics") {
	$table->head  = array ($strtopic, $strname);
	$table->align = array ("center", "left", "left", "left");
} else {
	$table->head  = array ($strname);
	$table->align = array ("left", "left", "left");
}

foreach ($openmeetings as $openmeetings) {
	if (!$openmeetings->visible) {
		// Show dimmed if the mod is hidden
		$link = "<a class=\"dimmed\" href=\"view.php?id=$openmeetings->coursemodule\">$openmeetings->name</a>";
	} else {
		// Show normal if the mod is visible
		$link = "<a href=\"view.php?id=$openmeetings->coursemodule\">$openmeetings->name</a>";
	}

	if ($course->format == "weeks" or $course->format == "topics") {
		$table->data[] = array ($openmeetings->section, $link);
	} else {
		$table->data[] = array ($link);
	}
}

echo "<br />";

print_table($table);

// Finish the page
print_footer($course);
