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
 * Provides class to restore OpenMeetings activity structure
 *
 * @package    mod_openmeetings
 * @category   backup
 * @license    Apache-2.0 GPL-3.0-only
 * @copyright  OpenMeetings devs
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/openmeetings/lib.php');

/**
 * Provides class to restore OpenMeetings activity structure
 */
class restore_openmeetings_activity_structure_step extends restore_activity_structure_step {
    /**
     * Structure definition
     */
    protected function define_structure() {
        $paths = array();
        $paths[] = new restore_path_element('openmeetings', '/activity/openmeetings');
        $paths[] = new restore_path_element('openmeetings_file', '/activity/openmeetings/files/file');
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Method to restore single OM activity DB item
     *
     * @param object $data - data to insert into DB
     */
    protected function process_openmeetings($data) {
        global $DB;

        $data = (object)$data;
        $data->course = $this->get_courseid();

        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // Insert the openmeetings record.
        $newitemid = $DB->insert_record('openmeetings', $data);
        $data->room_id = 0; // Reset it, new room will be created.
        $data->instance = $newitemid;
        openmeetings_update_instance($data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Method to restore single OM activity DB item
     *
     * @param object $data - data to insert into DB
     */
    protected function process_openmeetings_file($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->openmeetings_id = $this->get_new_parentid('openmeetings');
        $newitemid = $DB->insert_record('openmeetings_file', $data);
        $this->set_mapping('openmeetings_file', $oldid, $newitemid);
    }

    /**
     * Housekeeping
     */
    protected function after_execute() {
        // Add openmeetings related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_openmeetings', 'intro', null);
    }
}
