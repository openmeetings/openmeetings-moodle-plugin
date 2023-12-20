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
 * Class to perform REST calls using cURL
 *
 * @package    mod_openmeetings
 * @license    Apache-2.0 GPL-3.0-only
 * @copyright  OpenMeetings devs
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Enum holding constants for REST methods
 */
abstract class RestMethod {
    const GET = "GET";
    const POST = "POST";
    const DELETE = "DELETE";
}

/**
 * Class to perform REST calls using cURL
 */
class OmRestService {
    /**
     * @var array - REST config
     */
    private $config = array();
    /**
     * @var bool - if call was succesful
     */
    private $error = false;
    /**
     * @var string - error message
     */
    private $message = "";

    /**
     * Constructor
     *
     * @param array $cfg - config
     */
    public function __construct($cfg) {
        $this->config = $cfg;
    }

    /**
     * Method encodes params passed as multipart/form-data
     *
     * @param array $params - params to be encoded
     * @param string $boundary - boundary to use
     * @return string - encoded data
     */
    public static function encode($params, &$boundary) {
        $eol = "\r\n";
        if (!$boundary) {
            $boundary = md5(time());
        }
        $data = 'Content-type: multipart/form-data, boundary=' . $boundary . $eol . $eol;
        foreach ($params as $p) {
            $data .= '--' . $boundary . $eol;
            $data .= 'Content-Disposition: form-data; name="' . $p["name"] . '"' . $eol;
            if (array_key_exists('type', $p)) {
                $data .= 'Content-Type: ' . $p["type"] . $eol;
            }
            $data .= $eol . $p["val"] . $eol . $eol;
        }
        $data .= '--' . $boundary . $eol;
        return $data;
    }

    /**
     * Updates URL and/or options with params given
     *
     * @param string $url - REST url
     * @param RestMethod $method - REST method
     * @param string $sid - user SID
     * @param array $params - params to check
     * @param array $options - REST call options
     */
    private static function set_params(&$url, $method, $sid, $params, &$options) {
        $url .= '?';
        if ($sid) {
            $url .= '&sid=' . $sid;
        }
        if ($method == RestMethod::GET) {
            if ($params) {
                $url .= '&' . http_build_query($params, '', '&');
            }
        } else {
            // TODO something weird with PUT.
            $options[CURLOPT_POST] = true;
            if ($params) {
                $options[CURLOPT_POSTFIELDS] = $params;
            }
        }
    }

    /**
     * performs REST call
     *
     * @param string $url - REST url
     * @param RestMethod $method - REST method
     * @param string $sid - user SID
     * @param array $params - params to check
     * @param array $headers - REST headers
     * @param array $wrapername - specific part of response to be returned or any
     * @return array - response as array or nothing
     */
    public function call($url, $method, $sid, $params, $headers, $wrapername) {
        $options = array (
                CURLOPT_RETURNTRANSFER => true                            // Return web page.
                , CURLOPT_HEADER => false                                 // Return headers.
                , CURLOPT_FOLLOWLOCATION => true                          // Follow redirects.
                , CURLOPT_ENCODING => ""                                  // Handle all encodings.
                , CURLOPT_USERAGENT => "openmeetings"                     // Who am i.
                , CURLOPT_AUTOREFERER => true                             // Set referer on redirect.
                , CURLOPT_CONNECTTIMEOUT => 120                           // Timeout on connect.
                , CURLOPT_TIMEOUT => 120                                  // Timeout on response.
                , CURLOPT_MAXREDIRS => 10                                 // Stop after 10 redirects.
                , CURLOPT_SSL_VERIFYPEER => $this->config["checkpeer"]    // Enable/Disable SSL Cert checks.
                , CURLOPT_SSL_VERIFYHOST => $this->config["checkhost"]    // Enable/Disable hostname verification.
        );
        if ($headers) {
            $options[CURLOPT_HTTPHEADER] = $headers;
        }
        if ($method != RestMethod::GET && $method != RestMethod::POST) {
            $options[CURLOPT_CUSTOMREQUEST] = $method;
        }
        self::set_params($url, $method, $sid, $params, $options);
        $session = curl_init($url);
        curl_setopt_array($session, $options);

        $response = curl_exec($session);
        if (!$response) {
            $info = curl_getinfo($session);
            curl_close($session);
            $this->error = true;
            $this->message = 'Request OpenMeetings! OpenMeetings Service failed and no response was returned. Additional info: '
                    . $info;
            return;
        }
        // TODO FIXME check status.
        curl_close($session);
        $decoded = json_decode($response, true);
        return $wrapername ? $decoded[$wrapername] : $decoded;
    }

    /**
     * Checks if there was error
     *
     * @return bool - if there was error
     */
    public function is_error() {
        return $this->error;
    }

    /**
     * Get error message
     *
     * @return string - error message
     */
    public function get_message() {
        return $this->message;
    }
}
