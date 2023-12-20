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
 * Main view page for activity
 *
 * @package    mod_openmeetings
 * @license    Apache-2.0 GPL-3.0-only
 * @copyright  OpenMeetings devs
 */

require_once(__DIR__. '/../../config.php');
require_once(__DIR__. '/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course Module ID.
$g = optional_param('g', 0, PARAM_INT);

if (!empty($id)) {
    if (!$cm = get_coursemodule_from_id('openmeetings', $id)) {
        throw new moodle_exception('invalidcoursemodule');
    }
    if (!$course = $DB->get_record("course", array(
            "id" => $cm->course
    ))) {
        throw new moodle_exception('coursemisconf');
    }
    if (!$openmeetings = $DB->get_record("openmeetings", array(
            "id" => $cm->instance
    ))) {
        throw new moodle_exception('invalidid', 'openmeetings');
    }
} else if (!empty($g)) {
    if (!$openmeetings = $DB->get_record("openmeetings", array(
            "id" => $g
    ))) {
        throw new moodle_exception('invalidid', 'openmeetings');
    }
    if (!$course = $DB->get_record("course", array(
            "id" => $openmeetings->course
    ))) {
        throw new moodle_exception('invalidcourseid');
    }
    if (!$cm = get_coursemodule_from_instance("openmeetings", $openmeetings->id, $course->id)) {
        throw new moodle_exception('invalidcoursemodule');
    }
    $id = $cm->id;
} else {
    throw new moodle_exception('invalidid', 'openmeetings');
}

require_login($course->id);
$context = context_module::instance($cm->id);

$event = \mod_openmeetings\event\course_module_viewed::create(array(
        'objectid' => $openmeetings->id,
        'context' => $context,
));
$event->add_record_snapshot('openmeetings', $openmeetings);
$event->trigger();

$output = $PAGE->get_renderer('mod_openmeetings');
$openmeetingswidget = new openmeetings($openmeetings, false);

echo $output->header();
echo $output->render($openmeetingswidget);
echo $output->footer();
