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

require_once('OmRestService.php');

class OmGateway {
    private $sessionId = "";
    private $config = array();
    private $debug = false;

    function __construct($cfg) {
        $this->config = $cfg;
        $this->debug = true === $cfg["debug"];
    }

    function getRestUrl($name) {
        return $this->getUrl() . "/services/" . $name . "/";
    }

    function getUrl() {
        return $this->config["url"];
    }

    function version() {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->getRestUrl("info") . "version"
                , RestMethod::GET
                , null
                , array()
                , null
                , "info"
                );
        return $response;
    }

    private function showError($rest) {
        echo '<h2>Fault (Service error)</h2><pre>';
        if ($this->debug) {
            print_r($rest->getMessage());
        }
        echo '</pre>';
    }

    private function showServiceError($msg, $response) {
        echo '<h2>REST call failed</h2>';
        echo '<div>' . $msg . '; message: ' . $response['message'] . '</div>';
    }

    function login() {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->getRestUrl("user") . "login"
                , RestMethod::GET
                , null
                , array("user" => $this->config["user"], "pass" => $this->config["pass"])
                , null
                , "serviceResult"
            );

        if ($rest->isError()) {
            $this->showError($rest);
        } else {
            if ($response["type"] == "SUCCESS") {
                $this->sessionId = $response["message"];
                return true;
            } else {
                $this->showServiceError('Error While signing into OpenMeetings, please check credentials', $response);
            }
        }
        return false;
    }

    function getUser($login, $firstname, $lastname, $profilePictureUrl, $email, $userId) {
        return array(
            "login" => $login
            , "firstname" => $firstname
            , "lastname" => $lastname
            , "email" => $email
            , "profilePictureUrl" => $profilePictureUrl
            , "externalId" => $userId
            , "externalType" => $this->config["module"]
        );
    }

    function getSecureHash($user, $options) {
        $rest = new OmRestService($this->config);
        $options['externalType'] = $this->config["module"];
        if (!$this->config['recordingAllowed'] && array_key_exists('recordingId', $options)) {
            return -1;
        }
        $response = $rest->call(
                $this->getRestUrl("user") . "hash"
                , RestMethod::POST
                , $this->sessionId
                , http_build_query(array("user" => json_encode($user), "options" => json_encode($options)), '', '&')
                , null
                , "serviceResult"
            );

        if ($rest->isError()) {
            $this->showError($rest);
        } else {
            if ($response["type"] == "SUCCESS") {
                return $response["message"];
            } else {
                $this->showServiceError('Failed to get hash', $response);
            }
        }
        return -1;
    }

    function getRoom($roomId) {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->getRestUrl("room") . $roomId
                , RestMethod::GET
                , $this->sessionId
                , null
                , null
                , "roomDTO"
            );
        if ($rest->isError()) {
            $this->showError($rest);
        } else {
            if (isset($response["id"]) && $response["id"]) {
                return $response;
            } else {
                $this->showServiceError('Failed to get room', $response);
            }
        }
        return -1;
    }

    function updateRoom($data) {
        $data['externalType'] = $this->config["module"];
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->getRestUrl("room")
                , RestMethod::POST
                , $this->sessionId
                , array('room' => json_encode($data))
                , null
                , "roomDTO"
            );
        if ($rest->isError()) {
            $this->showError($rest);
        } else {
            if ($response["id"] > 0) {
                return $response["id"];
            } else {
                $this->showServiceError('Failed to update room', $response);
            }
        }
        return -1;
    }

    function deleteRoom($roomId) {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->getRestUrl("room") . $roomId
                , RestMethod::DELETE
                , $this->sessionId
                , ""
                , null
                , "serviceResult"
            );
        if ($rest->isError()) {
            $this->showError($rest);
        } else {
            if ($response["type"] == "SUCCESS") {
                return $response["code"];
            } else {
                $this->showServiceError('Failed to delete room', $response);
            }
        }
        return -1;
    }

    /**
     * Get list of available recordings made by this instance
     */
    function getRecordings() {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->getRestUrl("record") . rawurlencode($this->config["module"])
                , RestMethod::GET
                , $this->sessionId
                , ""
                , null
                , "recordingDTO"
            );
        if ($rest->isError()) {
            $this->showError($rest);
        } else {
            return $response;
        }
        return array();
    }

    function deleteRecording($recId) {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->getRestUrl("record") . $recId
                , RestMethod::DELETE
                , $this->sessionId
                , ""
                , null
                , "serviceResult"
            );
        if ($rest->isError()) {
            $this->showError($rest);
        } else {
            if ($response["type"] == "SUCCESS") {
                return $response["code"];
            } else {
                $this->showServiceError('Failed to delete recording', $response);
            }
        }
        return -1;
    }

    function cleanWb($roomId) {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->getRestUrl("room") . 'cleanwb/' . $roomId
                , RestMethod::GET
                , $this->sessionId
                , ""
                , null
                , "serviceResult"
                );
        if ($rest->isError()) {
            $this->showError($rest);
        } else {
            if ($response["type"] == "SUCCESS") {
                return $response["code"];
            } else {
                $this->showServiceError('Failed to clean WB', $response);
            }
        }
        return -1;
    }

    function getFiles() {
        $rest = new OmRestService($this->config);
        $response = $rest->call(
                $this->getRestUrl("file") . rawurlencode($this->config["module"])
                , RestMethod::GET
                , $this->sessionId
                , ""
                , null
                , "fileItemDTO"
                );
        if ($rest->isError()) {
            $this->showError($rest);
        } else {
            return $response;
        }
        return array();
    }

    function createFile($fileJson, $fileContents) {
        $fileJson['externalType'] = $this->config["module"];
        $rest = new OmRestService($this->config);
        $boundary = '';
        $params = array(
                array(
                    "name" => "file"
                    , "type" => "application/json"
                    , "val" => json_encode(array('fileItemDTO' => $fileJson))
                )
                , array(
                    "name" => "stream"
                    , "type" => "application/octet-stream"
                    , "val" => $fileContents
                )
            );
        $data = OmRestService::encode($params, $boundary);
        $response = $rest->call(
                $this->getRestUrl("file")
                , RestMethod::POST
                , $this->sessionId
                , $data
                , array("Content-Length: " . (strlen(bin2hex($data)) / 2), 'Content-Type: multipart/form-data; boundary=' . $boundary)
                , "fileItemDTO"
                );
        if ($rest->isError()) {
            $this->showError($rest);
        } else {
            return $response;
        }
        return false;
    }
}
