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
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    mod
 * @subpackage openmeetings
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Openmeetings conversion handler
 */
class moodle1_mod_openmeetings_handler extends moodle1_mod_handler {
	/**
	 * Declare the paths in moodle.xml we are able to convert
	 *
	 * The method returns list of {@link convert_path} instances. For each path returned,
	 * at least one of on_xxx_start(), process_xxx() and on_xxx_end() methods must be
	 * defined. The method process_xxx() is not executed if the associated path element is
	 * empty (i.e. it contains none elements or sub-paths only).
	 *
	 * Note that the path /MOODLE_BACKUP/COURSE/MODULES/MOD/OPENMEETINGS does not
	 * actually exist in the file. The last element with the module name was
	 * appended by the moodle1_converter class.
	 *
	 * @return array of {@link convert_path} instances
	 */
	public function get_paths() {
		return array(
				new convert_path('openmeetings', '/MOODLE_BACKUP/COURSE/MODULES/MOD/OPENMEETINGS')
		);
	}

	/**
	 * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/OPENMEETINGS
	 * data available
	 */
	public function process_openmeetings($data) {
	}

	/**
	 * This is executed every time we have one /MOODLE_BACKUP/COURSE/MODULES/MOD/OPENMEETINGS/OPTIONS/OPTION
	 * data available
	 */
	public function process_openmeetings_option($data) {
	}
}
