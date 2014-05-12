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
namespace mod_openmeetings\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_openmeetings course module viewed event class.
 *
 * @package    mod_openmeetings
 * @since      Moodle 2.7
 * @copyright  2014 solomax <solomax@apache.org>
 * @license    http://www.apache.org/licenses/LICENSE-2.0
 */
class course_module_viewed extends \core\event\course_module_viewed {
	protected function init() {
		$this->data['crud'] = 'r';
		$this->data['edulevel'] = self::LEVEL_PARTICIPATING;
		$this->data['objecttable'] = 'openmeetings';
	}
}
