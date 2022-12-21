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

defined('MOODLE_INTERNAL') || die();

require_once("OmGateway.php");

class OmRoomManager {
    private $config = array();

    public function __construct($cfg) {
        $this->config = $cfg;
    }

    public function update($data) {
        $gateway = new OmGateway($this->config);
        if ($gateway->login()) {
            return $gateway->updateRoom($data);
        } else {
            return -1;
        }
    }

    public function delete($roomid) {
        $gateway = new OmGateway($this->config);
        if ($gateway->login()) {
            return $gateway->deleteRoom($roomid);
        } else {
            return -1;
        }
    }

    public function get($roomid) {
        $gateway = new OmGateway($this->config);
        if ($gateway->login()) {
            return $gateway->getRoom($roomid);
        } else {
            return -1;
        }
    }
}
