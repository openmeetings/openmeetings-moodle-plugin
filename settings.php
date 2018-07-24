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

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configtext('openmeetings_host', get_string('host', 'openmeetings'), get_string('host', 'openmeetings'), "localhost", PARAM_TEXT));
$settings->add(new admin_setting_configtext('openmeetings_port', get_string('port', 'openmeetings'), get_string('port', 'openmeetings'), 5080, PARAM_INT));
$settings->add(new admin_setting_configtext('openmeetings_user', get_string('user', 'openmeetings'), get_string('user', 'openmeetings'), "admin", PARAM_TEXT));
$settings->add(new admin_setting_configpasswordunmask('openmeetings_pass', get_string('pass', 'openmeetings'), get_string('pass', 'openmeetings'), "password", PARAM_TEXT));
$settings->add(new admin_setting_configtext('openmeetings_moduleKey', get_string('moduleKey', 'openmeetings'), get_string('moduleKeyDesc', 'openmeetings'), "moodle", PARAM_TEXT));
$settings->add(new admin_setting_configtext('openmeetings_context', get_string('webapp', 'openmeetings'), get_string('webappDesc', 'openmeetings'), "openmeetings", PARAM_TEXT));
$settings->add(new admin_setting_configtext('openmeetings_protocol', get_string('protocol', 'openmeetings'), get_string('protocolDesc', 'openmeetings'), "http", PARAM_TEXT));
$settings->add(new admin_setting_configcheckbox('openmeetings_checkpeer', get_string('checkpeer', 'openmeetings'), get_string('checkpeerDesc', 'openmeetings'), 1));
$settings->add(new admin_setting_configcheckbox('openmeetings_checkhost', get_string('checkhost', 'openmeetings'), get_string('checkhostDesc', 'openmeetings'), 1));
