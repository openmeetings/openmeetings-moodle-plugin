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

function xmldb_openmeetings_upgrade($oldversion) {
	global $CFG, $DB, $OUTPUT;

	$dbman = $DB->get_manager();

	$result = true;

	$ver = 20111001;
	if ($oldversion < $ver) {
		// Define field allow_recording to be added to openmeetings
		$table = new xmldb_table('openmeetings');
		$field = new xmldb_field('allow_recording', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'room_recording_id');

		// Conditionally launch add field allow_recording
		if (!$dbman->field_exists($table, $field)) {
			$dbman->add_field($table, $field);
		}

		// openmeetings savepoint reached
		upgrade_mod_savepoint(true, $ver, 'openmeetings');
	}

	$ver = 20111002;
	if ($oldversion < $ver) {
		// Define field introformat to be dropped from openmeetings
		$table = new xmldb_table('openmeetings');
		$field = new xmldb_field('introformat');

		// Conditionally launch drop field introformat
		if ($dbman->field_exists($table, $field)) {
			$dbman->drop_field($table, $field);
		}

		// openmeetings savepoint reached
		upgrade_mod_savepoint(true, $ver, 'openmeetings');
	}

	$ver = 20111003;
	if ($oldversion < $ver) {
		upgrade_mod_savepoint(true, $ver, 'openmeetings');
	}

	$ver = 20120801;
	if ($oldversion < $ver) {
		upgrade_mod_savepoint(true, $ver, 'openmeetings');
	}

	$ver = 2014031603;
	if ($oldversion < $ver) {
		// Define field allow_recording to be added to openmeetings
		$table = new xmldb_table('openmeetings');
		$field = new xmldb_field('whole_window', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'allow_recording');

		// Conditionally launch add field allow_recording
		if (!$dbman->field_exists($table, $field)) {
			$dbman->add_field($table, $field);
		}

		upgrade_mod_savepoint(true, $ver, 'openmeetings');
	}

	$ver = 2016042002;
	if ($oldversion < $ver) {
		// Define field chat_hidden to be added to openmeetings
		$table = new xmldb_table('openmeetings');
		$field = new xmldb_field('chat_hidden', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'whole_window');

		// Conditionally launch add field chat_hidden
		if (!$dbman->field_exists($table, $field)) {
			$dbman->add_field($table, $field);
		}

		// Define field type to be changed in openmeetings
		$table = new xmldb_table('openmeetings');
		$field = new xmldb_field('type', XMLDB_TYPE_CHAR, '16', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'teacher');

		// Conditionally launch change type of the field type
		if ($dbman->field_exists($table, $field)) {
			$dbman->change_field_type($table, $field);
		}

		set_config('openmeetings_host', $CFG->openmeetings_red5host);
		set_config('openmeetings_port', $CFG->openmeetings_red5port);
		set_config('openmeetings_user', $CFG->openmeetings_openmeetingsAdminUser);
		set_config('openmeetings_pass', $CFG->openmeetings_openmeetingsAdminUserPass);
		set_config('openmeetings_moduleKey', $CFG->openmeetings_openmeetingsModuleKey);
		set_config('openmeetings_context', $CFG->openmeetings_webappname);
		unset_config('openmeetings_red5host');
		unset_config('openmeetings_red5port');
		unset_config('openmeetings_openmeetingsAdminUser');
		unset_config('openmeetings_openmeetingsAdminUserPass');
		unset_config('openmeetings_openmeetingsModuleKey');
		unset_config('openmeetings_webappname');

		upgrade_mod_savepoint(true, $ver, 'openmeetings');
	}

	$ver = 2017101000;
	if ($oldversion < $ver) {
		$table = new xmldb_table('openmeetings');
		$field = new xmldb_field('type');

		// Conditionally launch value change for `type` field
		if ($dbman->field_exists($table, $field)) {
			$DB->execute("UPDATE {$CFG->prefix}openmeetings SET type = 'presentation' WHERE type = 'restricted'");
		}
		upgrade_mod_savepoint(true, $ver, 'openmeetings');
	}
	$ver = 2018072401;
	if ($oldversion < $ver) {
		set_config('openmeetings_checkpeer', 1);
		set_config('openmeetings_checkhost', 1);
	}
	return $result;
}
