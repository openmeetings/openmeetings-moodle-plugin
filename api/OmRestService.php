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
 * Created on 03.01.2012 by eugen.schwert@gmail.com
 */

defined('MOODLE_INTERNAL') || die();

abstract class RestMethod
{
    const GET = "GET";
    const POST = "POST";
    const DELETE = "DELETE";
}

class OmRestService {
    private $config = array();
    private $error = false;
    private $message = "";

    function __construct($cfg) {
        $this->config = $cfg;
    }

    public static function encode($params, &$boundary) {
        $eol = "\r\n";
        if (!$boundary) {
            $boundary = md5(time());
        }
        $data = 'Content-type: multipart/form-data, boundary=' . $boundary . $eol . $eol;
        //
        foreach($params as $p) {
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

    private static function setParams(&$url, $method, $sid, $params, &$options) {
        $url .= '?';
        if ($sid) {
            $url .= '&sid=' . $sid;
        }
        if ($method == RestMethod::GET) {
            if ($params) {
                $url .= '&' . http_build_query($params, '', '&');
            }
        } else {
            //TODO something weird with PUT
            $options[CURLOPT_POST] = true;
            if ($params) {
                $options[CURLOPT_POSTFIELDS] = $params;
            }
        }
    }

    public function call($url, $method, $sid, $params, $headers, $wraperName) {
        $options = array (
                CURLOPT_RETURNTRANSFER => true                            // return web page
                , CURLOPT_HEADER => false                                 // return headers
                , CURLOPT_FOLLOWLOCATION => true                        // follow redirects
                , CURLOPT_ENCODING => ""                                // handle all encodings
                , CURLOPT_USERAGENT => "openmeetings"                    // who am i
                , CURLOPT_AUTOREFERER => true                            // set referer on redirect
                , CURLOPT_CONNECTTIMEOUT => 120                            // timeout on connect
                , CURLOPT_TIMEOUT => 120                                // timeout on response
                , CURLOPT_MAXREDIRS => 10                                // stop after 10 redirects
                , CURLOPT_SSL_VERIFYPEER => $this->config["checkpeer"]    // Enable/Disable SSL Cert checks
                , CURLOPT_SSL_VERIFYHOST => $this->config["checkhost"]    // Enable/Disable hostname verification
        );
        if ($headers) {
            $options[CURLOPT_HTTPHEADER] = $headers;
        }
        if ($method != RestMethod::GET && $method != RestMethod::POST) {
            $options[CURLOPT_CUSTOMREQUEST] = $method;
        }
        OmRestService::setParams($url, $method, $sid, $params, $options);
        $session = curl_init($url);
        curl_setopt_array($session, $options);

        $response = curl_exec($session);
        if (!$response) {
            $info = curl_getinfo($session);
            curl_close($session);
            $this->error = true;
            $this->message = 'Request OpenMeetings! OpenMeetings Service failed and no response was returned. Additional info: ' . print_object($info);
            return;
        }
        //TODO FIXME check status
        curl_close($session);
        $decoded = json_decode($response, true);
        return $wraperName ? $decoded[$wraperName] : $decoded;
    }

    public function isError() {
        return $this->error;
    }

    public function getMessage() {
        return $this->message;
    }
}
