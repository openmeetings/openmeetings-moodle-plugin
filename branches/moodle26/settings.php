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

defined('MOODLE_INTERNAL') || die();

$settings->add(new admin_setting_configtext('openmeetings_red5host', get_string('red5host', 'openmeetings'), get_string('red5host', 'openmeetings'), "localhost", PARAM_TEXT));

$settings->add(new admin_setting_configtext('openmeetings_red5port', get_string('red5port', 'openmeetings'), get_string('red5port', 'openmeetings'), 5080, PARAM_INT));

$settings->add(new admin_setting_configtext('openmeetings_openmeetingsAdminUser', get_string('openmeetingsAdminUser', 'openmeetings'), get_string('openmeetingsAdminUser', 'openmeetings'), "admin", PARAM_TEXT));

$settings->add(new admin_setting_configpasswordunmask('openmeetings_openmeetingsAdminUserPass', get_string('openmeetingsAdminUserPass', 'openmeetings'), get_string('openmeetingsAdminUserPass', 'openmeetings'), "password", PARAM_TEXT));

$settings->add(new admin_setting_configtext('openmeetings_openmeetingsModuleKey', get_string('openmeetingsModuleKeyLabel', 'openmeetings'), get_string('openmeetingsModuleKey', 'openmeetings'), "moodle", PARAM_TEXT));

$settings->add(new admin_setting_configtext('openmeetings_webappname', get_string('openmeetingsWebappnameLabel', 'openmeetings'), get_string('openmeetingsWebappnameDescription', 'openmeetings'), "openmeetings", PARAM_TEXT));

$settings->add(new admin_setting_configtext('openmeetings_protocol', get_string('openmeetingsProtocol', 'openmeetings'), get_string('openmeetingsProtocolDescription', 'openmeetings'), "http", PARAM_TEXT));
