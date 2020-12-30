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
 *
 * @package     mod_openmeetings
 * @category    backup
 */
defined('MOODLE_INTERNAL') || die;

class backup_openmeetings_activity_structure_step extends backup_activity_structure_step {
    protected function define_structure() {
        $room = new backup_nested_element('openmeetings', array('id'), array(
                'teacher', 'type', 'is_moderated_room', 'max_user', 'language', 'name', 'intro'
                , 'timecreated', 'timemodified', 'room_id', 'room_recording_id', 'allow_recording'
                , 'whole_window', 'chat_hidden'));

        $room->set_source_table('openmeetings', array('id' => backup::VAR_ACTIVITYID));
        $room->annotate_files('mod_openmeetings', 'intro', null); // This file area hasn't itemid

        $files = new backup_nested_element('files');
        $file = new backup_nested_element('file', array('id'), array(
                'openmeetings_id', 'file_id', 'file_name', 'wb'));

        $room->add_child($files);
        $files->add_child($file);

        $file->set_source_table('openmeetings_file', array('openmeetings_id' => backup::VAR_PARENTID));

        return $this->prepare_activity_structure($room);
    }
}
