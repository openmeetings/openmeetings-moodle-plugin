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
/**
 * This page lists all the instances of openmeetings in a particular course
 *
 * @author Sebastian Wagner
 * @version 1.7
 * @package mod_openmeetings
 **/
require(dirname(__FILE__).'/../../config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);   // Course

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

unset($id);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

// Get all required stringsopenmeetings
$stropenmeetings = get_string("modulenameplural", "openmeetings");
$stropenmeeting  = get_string("modulename", "openmeetings");
$strname         = get_string("name");
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_url('/mod/openmeetings/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$stropenmeetings);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($stropenmeetings);
echo $OUTPUT->header();

\mod_openmeetings\event\course_module_instance_list_viewed::create_from_course($course)->trigger();

// Get all the appropriate data
if (! $openmeetings = get_all_instances_in_course("openmeetings", $course)) {
    notice("There are no openmeetings", "../../course/view.php?id=$course->id");
    die;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $table->head  = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($openmeetings as $omeeting) {
    $cm = $modinfo->get_cm($omeeting->coursemodule);
    if ($usesections) {
        $printsection = '';
        if ($omeeting->section !== $currentsection) {
            if ($omeeting->section) {
                $printsection = get_section_name($course, $omeeting->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $omeeting->section;
        }
    } else {
        $printsection = html_writer::tag('span', userdate($omeeting->timemodified), array ('class' => 'smallinfo'));
    }

    $table->data[] = array (
            $printsection,
            html_writer::link(new moodle_url('view.php', array ('id' => $cm->id)), format_string($omeeting->name), array('target' => $omeeting->whole_window > 1 ? '_blank' : '_self')),
            format_module_intro('openmeetings', $omeeting, $cm->id)
    );
}

echo html_writer::table($table);

echo $OUTPUT->footer();
