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
//   http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing,
// software distributed under the License is distributed on an
// "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
// KIND, either express or implied.  See the License for the
// specific language governing permissions and limitations
// under the License.
/**
 *
 * @package     mod_openmeetings
 * @category    backup
 */
defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/openmeetings/backup/moodle2/restore_openmeetings_stepslib.php');

class restore_openmeetings_activity_task extends restore_activity_task {
    protected function define_my_settings() {
    }

    protected function define_my_steps() {
        $this->add_step(new restore_openmeetings_activity_structure_step('openmeetings_structure', 'openmeetings.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     *
     * @return array
     */
    static public function define_decode_contents() {
        $contents = array();
        $contents[] = new restore_decode_content('openmeetings', array('intro'), 'openmeetings');
        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        return array();
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * openmeetings logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();
        $rules[] = new restore_log_rule('openmeetings', 'add', 'view.php?id={course_module}', '{openmeetings}');
        $rules[] = new restore_log_rule('openmeetings', 'update', 'view.php?id={course_module}', '{openmeetings}');
        $rules[] = new restore_log_rule('openmeetings', 'view', 'view.php?id={course_module}', '{openmeetings}');
        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();
        $rules[] = new restore_log_rule('openmeetings', 'view all', 'index.php?id={course}', null);
        return $rules;
    }
}
