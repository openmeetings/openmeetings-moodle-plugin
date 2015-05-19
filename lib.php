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

$old_error_handler = set_error_handler("myErrorHandler");
require_once($CFG->dirroot.'/config.php');
require_once($CFG->dirroot.'/mod/openmeetings/api/openmeetings_gateway.php');

//include('../mod/openmeetings/lib/nusoap.php');
// error handler function
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    switch ($errno) {
    case E_USER_ERROR:
        echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
        echo "  Fatal error on line $errline in file $errfile";
        echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
        echo "Aborting...<br />\n";
        exit(1);
        break;

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

function getRecordingHash($gateway, $recId) {
	global $USER, $CFG;
	
	return $gateway->setUserObjectAndGenerateRecordingHashByURL($USER->username, $USER->firstname, $USER->lastname
		, $USER->id, $CFG->openmeetings_openmeetingsModuleKey, $recId);
}

function getOmConfig() {
	global $CFG;
	return array("protocol" => $CFG->openmeetings_protocol, "port" => $CFG->openmeetings_red5port
		, "host" => $CFG->openmeetings_red5host, "webappname" => $CFG->openmeetings_webappname
		, "adminUser" => $CFG->openmeetings_openmeetingsAdminUser
		, "adminPass" => $CFG->openmeetings_openmeetingsAdminUserPass
		, "moduleKey" => $CFG->openmeetings_openmeetingsModuleKey);
}

function setRoomName(&$openmeetings) {
	$openmeetings->roomname = 'MOODLE_COURSE_ID_' . $openmeetings->course . '_NAME_' . $openmeetings->name;
}

function openmeetings_add_instance($openmeetings) {
	global $USER, $CFG, $DB;
	
	$openmeetings_gateway = new openmeetings_gateway(getOmConfig());
	if ($openmeetings_gateway->loginuser()) {
		
		//Roomtype 0 means its and recording, we don't need to create a room for that
		if ($openmeetings->type != 0) {
			setRoomName($openmeetings);
			$openmeetings->room_id = $openmeetings_gateway->createRoomWithModAndType($openmeetings);
		}
		
	} else {
		echo "Could not login User to OpenMeetings, check your OpenMeetings Module Configuration";
		exit();
	}

    # May have to add extra stuff in here #
    return $DB->insert_record("openmeetings", $openmeetings);
}


function openmeetings_update_instance($openmeetings) {
	global $DB, $CFG;
	
	$openmeetings->timemodified = time();
	$openmeetings->id = $openmeetings->instance;

	$openmeetings_gateway = new openmeetings_gateway(getOmConfig());
	if ($openmeetings_gateway->loginuser()) {
		
		//Roomtype 0 means its and recording, we don't need to update a room for that
		if ($openmeetings->type != 0) {
			setRoomName($openmeetings);
			$openmeetings->room_id = $openmeetings_gateway->updateRoomWithModeration($openmeetings);
		} else {
			$openmeetings->room_id = 0;
		}
		
	} else {
		echo "Could not login User to OpenMeetings, check your OpenMeetings Module Configuration";
		exit();
	}

	# May have to add extra stuff in here #
	return $DB->update_record("openmeetings", $openmeetings);
}


function openmeetings_delete_instance($id) {
	global $DB, $CFG;
	
	if (!$openmeetings = $DB->get_record("openmeetings", array("id" => "$id"))) {
		return false;
	}

	$result = true;

	$openmeetings_gateway = new openmeetings_gateway(getOmConfig());
	if ($openmeetings_gateway->loginuser()) {
		
		//Roomtype 0 means its and recording, we don't need to update a room for that
		if ($openmeetings->type != 0) {
			$openmeetings->room_id = $openmeetings_gateway->deleteRoom($openmeetings);
		}
		
	} else {
		echo "Could not login User to OpenMeetings, check your OpenMeetings Module Configuration";
		exit();
	}
	
	# Delete any dependent records here #
	if (!$DB->delete_records("openmeetings", array("id" => "$openmeetings->id"))) {
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
	$info->name = format_string($meeting->name);
	$info->onclick = "window.open('" . new moodle_url('/mod/openmeetings/view.php', array ('id' => $coursemodule->id)) . "');return false;";
	return $info;
}

function openmeetings_user_outline($course, $user, $mod, $openmeetings) {
    return true;
}


function openmeetings_user_complete($course, $user, $mod, $openmeetings) {
    return true;
}


function openmeetings_print_recent_activity($course, $isteacher, $timestart) {
    global $CFG;

    return false;  //  True if anything was printed, otherwise false 
}


function openmeetings_cron () {
    global $CFG;

    return true;
}


function openmeetings_grades($openmeetingsid) {
   return NULL;
}


function openmeetings_get_participants($openmeetingsid) {
    return false;
}

function openmeetings_scale_used ($openmeetingsid,$scaleid) {
    $return = false;

    return $return;
}

function openmeetings_scale_used_anywhere($scaleid) {
    return false;
}
