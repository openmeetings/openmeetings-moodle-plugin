<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// Licensed to the Apache Software Foundation (ASF) under one
// or more contributor license agreements.  See the NOTICE file
// distributed with this work for additional information
// regarding copyright ownership.  The ASF licenses this file
// to you under the Apache License, Version 2.0 (the
// "License") +  you may not use this file except in compliance
// with the License.  You may obtain a copy of the License at
//
// http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing,
// software distributed under the License is distributed on an
// "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
// KIND, either express or implied.  See the License for the
// specific language governing permissions and limitations
// under the License.

/**
 * OpenMeetings activity module common functions
 *
 * @package    mod_openmeetings
 * @license    Apache-2.0 GPL-3.0-only
 * @copyright  OpenMeetings devs
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__. '/../../config.php');
set_error_handler("my_error_handler");
require_once(__DIR__. '/api/OmGateway.php');

/**
 * error handler function
 *
 * @SuppressWarnings(PHPMD.ExitExpression)
 */
function my_error_handler($errno, $errstr, $errfile, $errline) {
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
            break;
    }
    // Don't execute PHP internal error handler.
    return true;
}

/**
 * Constructs OM user based on Moodle logged-in user
 *
 * @param OmGateway $gateway - gateway
 * @return array - user array
 */
function get_om_user($gateway) {
    global $USER;
    $pictureurl = moodle_url::make_pluginfile_url(context_user::instance($USER->id)->id, 'user', 'icon'
            , null, '/', 'f1')->out(false);
    return $gateway->get_user($USER->username, $USER->firstname, $USER->lastname, $pictureurl, $USER->email, $USER->id);
}

/**
 * Retrieves secure hash for OM room
 *
 * @param OmGateway $gateway - gateway
 * @param array $options - external options
 * @return string - secure hash of the room or -1 in case of error
 */
function get_om_hash($gateway, $options) {
    return $gateway->get_secure_hash(get_om_user($gateway), $options);
}

/**
 * Constructs gateway config
 *
 * @return array - config as array
 */
function get_om_config() {
    global $CFG;
    return array(
        'url' => $CFG->openmeetings_url
        , 'user' => $CFG->openmeetings_user
        , 'pass' => $CFG->openmeetings_pass
        , 'module' => $CFG->openmeetings_moduleKey
        , 'recordingAllowed' => $CFG->openmeetings_recordingAllowed
        , 'checkpeer' => 1 == $CFG->openmeetings_checkpeer
        , 'checkhost' => 1 == $CFG->openmeetings_checkhost
        , 'debug' => $CFG->debug > 0
    );
}

/**
 * Sets room name for OM activity
 *
 * @param stdclass $meeting - OM activity
 */
function set_room_name(&$meeting) {
    $meeting->roomname = $meeting->course . ' ' . $meeting->name;
}

/**
 * Creates room array from DB object
 *
 * @param stdclass $meeting - OM activity
 * @return array - room as array
 */
function get_room(&$meeting) {
    global $CFG;
    set_room_name($meeting);
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
            , 'allowRecording' => $CFG->openmeetings_recordingAllowed && 1 == $meeting->allow_recording
            , 'chatHidden' => 1 == $meeting->chat_hidden
            , 'externalId' => $meeting->id
            , 'files' => array()
    );
}

/**
 * Add OM DB record.
 *
 * @param stdclass $meeting - OM activity
 * @return int - room ID or -1 in case of error
 * @SuppressWarnings(PHPMD.ExitExpression)
 */
function openmeetings_add_instance(&$meeting) {
    global $DB;

    $gateway = new OmGateway(get_om_config());
    if (!$gateway->login()) {
        die("Could not login User to OpenMeetings, check your OpenMeetings Module Configuration");
    }
    $meeting->id = $DB->insert_record("openmeetings", $meeting);
    return update_om_room_obj($meeting, $gateway);
}

/**
 * Update OM DB record.
 *
 * @param stdclass $meeting - OM activity
 * @return int - room ID or -1 in case of error
 * @SuppressWarnings(PHPMD.ExitExpression)
 */
function openmeetings_update_instance(&$meeting) {
    $gateway = new OmGateway(get_om_config());
    if (!$gateway->login()) {
        die("Could not login User to OpenMeetings, check your OpenMeetings Module Configuration");
    }
    $meeting->timemodified = time();
    $meeting->id = $meeting->instance;
    return update_om_room_obj($meeting, $gateway);
}

/**
 * Create files and updates OM activity in Moodle DB
 *
 * @param stdclass $meeting - OM activity
 * @param OmGateway $gateway - gateway
 * @SuppressWarnings(PHPMD.ExitExpression)
 */
function update_om_room(&$meeting, $gateway) {
    global $DB, $mform;
    $room = get_room($meeting);
    foreach ($meeting->remove as $mfileid => $selected) {
        if ($selected == 0) {
            unset($meeting->remove[$mfileid]);
        }
    }
    if (!empty($meeting->remove)) {
        $delids = join(',', $meeting->remove);
        $DB->delete_records_select('openmeetings_file', 'id IN (' . $delids . ')');
    }
    foreach ($DB->get_records('openmeetings_file', array('openmeetings_id' => $meeting->id)) as $mfile) {
        $room['files'][] = array('wbIdx' => $mfile->wb, 'fileId' => $mfile->file_id);
    }
    for ($i = 0; $i < $meeting->room_files; ++$i) {
        $wbidx = $meeting->wb_idx[$i];
        $omfileid = $meeting->om_files[$i];
        $fileobj = new stdClass();
        $fileobj->openmeetings_id = $meeting->id;
        $fileobj->wb = $wbidx;
        if ($omfileid > 0) {
            $fileobj->file_name = $meeting->{'om_int_file' . $omfileid};
            $fileobj->file_id = $omfileid;
            $fileobj->id = $DB->insert_record("openmeetings_file", $fileobj);
            $room['files'][] = array('wbIdx' => $wbidx, 'fileId' => $omfileid);
            continue;
        }
        $file = $mform->getFile($i);
        if (!!$file) {
            $filename = $file->get_filename();
            $fileobj->file_name = $filename;
            $fileobj->file_id = 0;
            $fileobj->id = $DB->insert_record("openmeetings_file", $fileobj);
            $filejson = array(
                    'externalId' => $fileobj->id
                    , 'name' => $filename
            );
            $filecontent = $file->get_content();
            $omfile = $gateway->create_file($filejson, $filecontent);
            if (!$omfile) {
                $DB->delete_records("openmeetings_file", array("id" => $fileobj->id));
            } else {
                $fileobj->file_id = $omfile['id'];
                $DB->update_record("openmeetings_file", $fileobj);
                $room['files'][] = array('wbIdx' => $wbidx, 'fileId' => $omfile['id']);
            }
        }
    }
    $meeting->room_id = $gateway->update_room($room);
}

/**
 * Updates room ID for OM activity
 *
 * @param stdclass $meeting - OM activity
 * @param OmGateway $gateway - gateway
 * @return int - OM activity ID
 * @SuppressWarnings(PHPMD.ExitExpression)
 */
function update_om_room_obj(&$meeting, $gateway) {
    global $DB;
    if ($meeting->type == 'recording') {
        $meeting->room_id = 0;
    } else {
        update_om_room($meeting, $gateway);
    }
    $DB->update_record("openmeetings", $meeting); // Need to update room_id.
    return $meeting->id;
}

/**
 * Delete OM DB record from everywhere.
 *
 * @param int $id - OM activity ID
 * @return bool - if operation was succesful
 * @SuppressWarnings(PHPMD.ExitExpression)
 */
function openmeetings_delete_instance($id) {
    global $DB;

    if (!$meeting = $DB->get_record("openmeetings", array("id" => "$id"))) {
        return false;
    }

    $result = true;

    $gateway = new OmGateway(get_om_config());
    if (!$gateway->login()) {
        die("Could not login User to OpenMeetings, check your OpenMeetings Module Configuration");
    }
    if ($meeting->type != 'recording') {
        $meeting->room_id = $gateway->delete_room($meeting->room_id);
    }
    // Processing room files.
    $DB->delete_records("openmeetings_file", array("openmeetings_id" => $meeting->id));
    // Delete room instance.
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
 * @param object $coursemodule - course
 * @return object info - cm info
 */
function openmeetings_get_coursemodule_info($coursemodule) {
    global $DB;

    if (!$meeting = $DB->get_record('openmeetings', array ('id' => $coursemodule->instance))) {
        return null;
    }

    $info = new cached_cm_info();
    $info->name = $meeting->name;
    $info->afterlink = $meeting->intro;
    $info->onclick = "window.open('"
        . new moodle_url('/mod/openmeetings/view.php', array('id' => $coursemodule->id))
        . "', '" . ($meeting->whole_window > 1 ? 'OmMoodleActivity' : '_self') . "');return false;";
    return $info;
}

/**
 * N/A
 *
 * @return bool
 */
function openmeetings_user_outline() {
    return true;
}

/**
 * N/A
 *
 * @return bool
 */
function openmeetings_user_complete() {
    return true;
}

/**
 * N/A
 *
 * @return bool
 */
function openmeetings_print_recent_activity() {
    return false;  // True if anything was printed, otherwise false.
}

/**
 * N/A
 *
 * @return bool
 */
function openmeetings_cron() {
    return true;
}

/**
 * N/A
 *
 * @return bool
 */
function openmeetings_grades() {
    return null;
}

/**
 * N/A
 *
 * @return bool
 */
function openmeetings_get_participants() {
    return false;
}

/**
 * N/A
 *
 * @return bool
 */
function openmeetings_scale_used() {
    return false;
}

/**
 * N/A
 *
 * @return bool
 */
function openmeetings_scale_used_anywhere() {
    return false;
}

/**
 * Enables grading using Moodle's Activity completion API.
 *
 * @return bool - if feature is supported
 */
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
