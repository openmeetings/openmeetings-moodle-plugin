<?php

defined('MOODLE_INTERNAL') || die;


$settings->add(new admin_setting_configtext('openmeetings_red5host', get_string('red5host', 'openmeetings'),
                       get_string('red5host', 'openmeetings'), "localhost", PARAM_TEXT));

$settings->add(new admin_setting_configtext('openmeetings_red5port', get_string('red5port', 'openmeetings'),
                   get_string('red5port', 'openmeetings'), 5080, PARAM_INT));

$settings->add(new admin_setting_configtext('openmeetings_openmeetingsAdminUser', get_string('openmeetingsAdminUser', 'openmeetings'),
                       get_string('openmeetingsAdminUser', 'openmeetings'), "admin", PARAM_TEXT));

$settings->add(new admin_setting_configpasswordunmask('openmeetings_openmeetingsAdminUserPass', get_string('openmeetingsAdminUserPass', 'openmeetings'),
                       get_string('openmeetingsAdminUserPass', 'openmeetings'), "password", PARAM_TEXT));

$settings->add(new admin_setting_configtext('openmeetings_openmeetingsModuleKey', get_string('openmeetingsModuleKeyLabel', 'openmeetings'),
                       get_string('openmeetingsModuleKey', 'openmeetings'), "moodle", PARAM_TEXT));

$settings->add(new admin_setting_configtext('openmeetings_webappname', get_string('openmeetingsWebappnameLabel', 'openmeetings'),
						get_string('openmeetingsWebappnameDescription', 'openmeetings'), "openmeetings", PARAM_TEXT));

