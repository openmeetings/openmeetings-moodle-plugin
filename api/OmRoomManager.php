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
 * Some high level OM methods
 *
 * @package    mod_openmeetings
 * @license    Apache-2.0 GPL-3.0-only
 * @copyright  OpenMeetings devs
 */

defined('MOODLE_INTERNAL') || die();

require_once("OmGateway.php");

/**
 * Class defining high level OM methods
 */
class OmRoomManager {
    /**
     * @var array - room manager config
     */
    private $config = array();

    /**
     * Constructor
     *
     * @param array $cfg - room manager config
     */
    public function __construct($cfg) {
        $this->config = $cfg;
    }

    /**
     * Updates OM room with some new details (name, options etc.)
     *
     * @param array $data - room details/options
     * @return int - OM room ID or -1 in case of error
     */
    public function update($data) {
        $gateway = new OmGateway($this->config);
        if ($gateway->login()) {
            return $gateway->update_room($data);
        } else {
            return -1;
        }
    }

    /**
     * Delete OM room by Moodle room ID
     *
     * @param int $roomid - Moodle room ID
     * @return int - response code or -1 in case of error
     */
    public function delete($roomid) {
        $gateway = new OmGateway($this->config);
        if ($gateway->login()) {
            return $gateway->delete_room($roomid);
        } else {
            return -1;
        }
    }

    /**
     * Retrieves OM room ID by Moodle room ID
     *
     * @param int $roomid - Moodle room ID
     * @return int - OM room ID or -1 in case of error
     */
    public function get($roomid) {
        $gateway = new OmGateway($this->config);
        if ($gateway->login()) {
            return $gateway->get_room($roomid);
        } else {
            return -1;
        }
    }
}
