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

function xmldb_openmeetings_upgrade($oldversion) {

    global $CFG, $DB, $OUTPUT;
    
    $dbman = $DB->get_manager();

    $result = true;
    
    if ($oldversion < 20111001) {
    
    	// Define field allow_recording to be added to openmeetings
    	$table = new xmldb_table('openmeetings');
    	$field = new xmldb_field('allow_recording', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'room_recording_id');
    
    	// Conditionally launch add field allow_recording
    	if (!$dbman->field_exists($table, $field)) {
    		$dbman->add_field($table, $field);
    	}
    
    	// openmeetings savepoint reached
    	upgrade_mod_savepoint(true, 20111001, 'openmeetings');
    }
    
    if ($oldversion < 20111002) {
    
    	// Define field introformat to be dropped from openmeetings
    	$table = new xmldb_table('openmeetings');
    	$field = new xmldb_field('introformat');
    
    	// Conditionally launch drop field introformat
    	if ($dbman->field_exists($table, $field)) {
    		$dbman->drop_field($table, $field);
    	}
    
    	// openmeetings savepoint reached
    	upgrade_mod_savepoint(true, 20111002, 'openmeetings');
    }
    
    if ($oldversion < 20111003) {
    	upgrade_mod_savepoint(true, 20111003, 'openmeetings');
    }
    
    if ($oldversion < 20120801) {
    	upgrade_mod_savepoint(true, 20120801, 'openmeetings');
    }
    
    if ($oldversion == 20120801) {
    	//$result = false;
    	//nothing to upgrade here
    	
    }
    
    return $result;
}



?>
