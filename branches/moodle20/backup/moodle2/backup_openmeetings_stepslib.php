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

/**
 * Define all the backup steps that will be used by the backup_openmeetings_activity_task
 *
 * @package    mod
 * @subpackage openmeetings
 * @copyright  2010 onwards Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

 /**
 * Define the complete openmeetings structure for backup, with file and id annotations
 */
class backup_openmeetings_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        //the openmeetings module stores no user info

        // Define each element separated
        $openmeetings = new backup_nested_element('openmeetings', array('id'),
                array(
                        'course',
                        'teacher',
                        'type',
                        'is_moderated_room',
                        'max_user',
                        'language',
                        'name',
                        'intro',
                        'introformat',
                        'timecreated',
                        'timemodified',
                        'room_id',
                        'room_recording_id',
                        'allow_recording',
                        'output_settings'));


        // Build the tree
        //nothing here for openmeetingss

        // Define sources
        $openmeetings->set_source_table('openmeetings', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        //module has no id annotations

        // Define file annotations
        $openmeetings->annotate_files('mod_openmeetings', 'intro', null); // This file area hasn't itemid

        // Return the root element (openmeetings), wrapped into standard activity structure
        return $this->prepare_activity_structure($openmeetings);

    }
}
