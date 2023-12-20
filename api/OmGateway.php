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
 * Class responsible for communications with OM
 * Prepares data for OM REST call, and processes the results
 *
 * @package    mod_openmeetings
 * @license    Apache-2.0 GPL-3.0-only
 * @copyright  OpenMeetings devs
 */

defined('MOODLE_INTERNAL') || die();

require_once('OmRestService.php');

/**
 * Class responsible for communications with OM
 * Prepares data for OM REST call, and processes the results
 */
class OmGateway {
    /**
     * @var string OM sessionID
     */
    private $sessionid = "";
    /**
     * @var array Gateway config
     */
    private $config = array();
    /**
     * @var bool debug flag
     */
    private $debug = false;

    /**
     * Constructor
     *
     * @param array $cfg - Gateway config
     */
    public function __construct($cfg) {
        $this->config = $cfg;
        $this->debug = true === $cfg["debug"];
    }

    /**
     * Method to get URL for specific REST endpoint
     *
     * @param string $name - the name of the endpoint
     * @return string - URL
     */
    private function get_rest_url($name) {
        return $this->get_url() . "/services/" . $name . "/";
    }

    /**
     * Method to get OM URL
     *
     * @return string - OM URL
     */
    public function get_url() {
        return $this->config["url"];
    }

    /**
     * Method to get response from OM version endpoint
     *
     * @return array - response from OM version endpoint
     */
    public function version() {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->get_rest_url("info") . "version"
                , RestMethod::GET
                , null
                , array()
                , null
                , "info"
                );
        return $response;
    }

    /**
     * Displays an error
     *
     * @param array $rest - REST error
     */
    private function show_error($rest) {
        echo '<h2>Fault (Service error)</h2><pre>';
        if ($this->debug) {
            echo($rest->get_message());
        }
        echo '</pre>';
    }

    /**
     * Displays service error
     *
     * @param string $msg - message
     * @param array $response - REST response
     */
    private function show_service_error($msg, $response) {
        echo '<h2>REST call failed</h2>';
        echo '<div>' . $msg . '; message: ' . $response['message'] . '</div>';
    }

    /**
     * Method to perform login to OM server
     *
     * @return bool - if login was successful or not
     */
    public function login() {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->get_rest_url("user") . "login"
                , RestMethod::GET
                , null
                , array("user" => $this->config["user"], "pass" => $this->config["pass"])
                , null
                , "serviceResult"
            );

        if ($rest->is_error()) {
            $this->show_error($rest);
        } else {
            if ($response["type"] == "SUCCESS") {
                $this->sessionId = $response["message"];
                return true;
            } else {
                $this->show_service_error('Error While signing into OpenMeetings, please check credentials', $response);
            }
        }
        return false;
    }

    /**
     * Constructs OM user based on passed params
     *
     * @param string $login - user's login
     * @param string $firstname - user's first name
     * @param string $lastname - user's last name
     * @param string $pictureurl - URL of user's avatar
     * @param string $email - user's email
     * @param string $userid - The ID of user in Moodle
     * @return array - user array
     */
    public function get_user($login, $firstname, $lastname, $pictureurl, $email, $userid) {
        return array(
            "login" => $login
            , "firstname" => $firstname
            , "lastname" => $lastname
            , "email" => $email
            , "profilePictureUrl" => $pictureurl
            , "externalId" => $userid
            , "externalType" => $this->config["module"]
        );
    }

    /**
     * Retrieves secure hash for OM room
     *
     * @param array $user - Moodle user
     * @param array $options - external options
     * @return string - secure hash of the room or -1 in case of error
     */
    public function get_secure_hash($user, $options) {
        $rest = new OmRestService($this->config);
        $options['externalType'] = $this->config["module"];
        if (!$this->config['recordingAllowed'] && array_key_exists('recordingId', $options)) {
            return -1;
        }
        $response = $rest->call(
                $this->get_rest_url("user") . "hash"
                , RestMethod::POST
                , $this->sessionId
                , http_build_query(array("user" => json_encode($user), "options" => json_encode($options)), '', '&')
                , null
                , "serviceResult"
            );

        if ($rest->is_error()) {
            $this->show_error($rest);
        } else {
            if ($response["type"] == "SUCCESS") {
                return $response["message"];
            } else {
                $this->show_service_error('Failed to get hash', $response);
            }
        }
        return -1;
    }

    /**
     * Retrieves OM room ID by Moodle room ID
     *
     * @param int $roomid - Moodle room ID
     * @return int - OM room ID or -1 in case of error
     */
    public function get_room($roomid) {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->get_rest_url("room") . $roomid
                , RestMethod::GET
                , $this->sessionId
                , null
                , null
                , "roomDTO"
            );
        if ($rest->is_error()) {
            $this->show_error($rest);
        } else {
            if (isset($response["id"]) && $response["id"]) {
                return $response;
            } else {
                $this->show_service_error('Failed to get room', $response);
            }
        }
        return -1;
    }

    /**
     * Updates OM room with some new details (name, options etc.)
     *
     * @param array $data - room details/options
     * @return int - OM room ID or -1 in case of error
     */
    public function update_room($data) {
        $data['externalType'] = $this->config["module"];
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->get_rest_url("room")
                , RestMethod::POST
                , $this->sessionId
                , array('room' => json_encode($data))
                , null
                , "roomDTO"
            );
        if ($rest->is_error()) {
            $this->show_error($rest);
        } else {
            if ($response["id"] > 0) {
                return $response["id"];
            } else {
                $this->show_service_error('Failed to update room', $response);
            }
        }
        return -1;
    }

    /**
     * Delete OM room by Moodle room ID
     *
     * @param int $roomid - Moodle room ID
     * @return int - response code or -1 in case of error
     */
    public function delete_room($roomid) {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->get_rest_url("room") . $roomid
                , RestMethod::DELETE
                , $this->sessionId
                , ""
                , null
                , "serviceResult"
            );
        if ($rest->is_error()) {
            $this->show_error($rest);
        } else {
            if ($response["type"] == "SUCCESS") {
                return $response["code"];
            } else {
                $this->show_service_error('Failed to delete room', $response);
            }
        }
        return -1;
    }

    /**
     * Get list of available recordings made by this instance
     *
     * @return array - list of available recordings
     */
    public function get_recordings() {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->get_rest_url("record") . rawurlencode($this->config["module"])
                , RestMethod::GET
                , $this->sessionId
                , ""
                , null
                , "recordingDTO"
            );
        if ($rest->is_error()) {
            $this->show_error($rest);
        } else {
            return $response;
        }
        return array();
    }

    /**
     * Delete OM recording by ID
     *
     * @param int $recid - recording ID
     * @return int - response code or -1 in case of error
     */
    public function delete_recording($recid) {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->get_rest_url("record") . $recid
                , RestMethod::DELETE
                , $this->sessionId
                , ""
                , null
                , "serviceResult"
            );
        if ($rest->is_error()) {
            $this->show_error($rest);
        } else {
            if ($response["type"] == "SUCCESS") {
                return $response["code"];
            } else {
                $this->show_service_error('Failed to delete recording', $response);
            }
        }
        return -1;
    }

    /**
     * Performs WB cleanup for specific room
     *
     * @param int $roomid - Moodle room ID
     * @return int - response code or -1 in case of error
     */
    public function clean_wb($roomid) {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->get_rest_url("wb") . 'resetwb/' . $roomid
                , RestMethod::GET
                , $this->sessionId
                , ""
                , null
                , "serviceResult"
                );
        if ($rest->is_error()) {
            $this->show_error($rest);
        } else {
            if ($response["type"] == "SUCCESS") {
                return $response["code"];
            } else {
                $this->show_service_error('Failed to clean WB', $response);
            }
        }
        return -1;
    }

    /**
     * Get list of available files
     *
     * @return array - list of available files
     */
    public function get_files() {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->get_rest_url("file") . rawurlencode($this->config["module"])
                , RestMethod::GET
                , $this->sessionId
                , ""
                , null
                , "fileItemDTO"
                );
        if ($rest->is_error()) {
            $this->show_error($rest);
        } else {
            return $response;
        }
        return array();
    }

    /**
     * Creates file at OM
     *
     * @param array $json - file params
     * @param object $contents - file contents
     * @return bool - if operation was succesful
     */
    public function create_file($json, $contents) {
        $json['externalType'] = $this->config["module"];
        $rest = new OmRestService($this->config);
        $boundary = '';
        $params = array(
                array(
                    "name" => "file"
                    , "type" => "application/json"
                    , "val" => json_encode(array('fileItemDTO' => $json))
                )
                , array(
                    "name" => "stream"
                    , "type" => "application/octet-stream"
                    , "val" => $contents
                )
            );
        $data = OmRestService::encode($params, $boundary);
        $response = $rest->call(
                $this->get_rest_url("file")
                , RestMethod::POST
                , $this->sessionId
                , $data
                , array("Content-Length: " . (strlen(bin2hex($data)) / 2)
                        , 'Content-Type: multipart/form-data; boundary=' . $boundary)
                , "fileItemDTO"
                );
        if ($rest->is_error()) {
            $this->show_error($rest);
        } else {
            return $response;
        }
        return false;
    }
}
