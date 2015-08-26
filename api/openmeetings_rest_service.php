<?php
/**
 * you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * It is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You can get a copy of the GNU General Public License
 * at <http://www.gnu.org/licenses/>.
 */
/**
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
/*
 * Created on 03.01.2012 by eugen.schwert@gmail.com
 */
class openmeetings_rest_service {
	function call($request, $returnAttribute = "return") {
		// This will allow you to view errors in the browser
		// Note: set "display_errors" to 0 in production
		// ini_set('display_errors',1);
		
		// Report all PHP errors (notices, errors, warnings, etc.)
		// error_reporting(E_ALL);
		
		// URI used for making REST call. Each Web Service uses a unique URL.
		// $request
		
		// Initialize the session by passing the request as a parameter
		$options = array (
				CURLOPT_RETURNTRANSFER => true			// return web page
				, CURLOPT_HEADER => true 				// return headers
				, CURLOPT_FOLLOWLOCATION => true		// follow redirects
				, CURLOPT_ENCODING => ""				// handle all encodings
				, CURLOPT_USERAGENT => "openmeetings"	// who am i
				, CURLOPT_AUTOREFERER => true			// set referer on redirect
				, CURLOPT_CONNECTTIMEOUT => 120			// timeout on connect
				, CURLOPT_TIMEOUT => 120				// timeout on response
				, CURLOPT_MAXREDIRS => 10					// stop after 10 redirects
				//, CURLOPT_SSL_VERIFYPEER => false			// Disabled SSL Cert checks
				//, CURLOPT_SSL_VERIFYHOST => false			// Disables hostname verification
		);
		$session = curl_init($request);
		curl_setopt_array($session, $options);
		
		// Make the request
		$response = curl_exec($session);
		
		// Confirm that the request was transmitted to the OpenMeetings! Image Search Service
		if (!$response) {
			$err = curl_errno($session);
			$errmsg = curl_error($session);
			$header = curl_getinfo($session);
			die("Request OpenMeetings! OpenMeetings Service failed and no response was returned.");
		}
		// Close the curl session
		curl_close($session);
		
		// Create an array to store the HTTP response codes
		$status_code = array ();
		
		// Use regular expressions to extract the code from the header
		preg_match('/\d\d\d/', $response, $status_code);
		
		// Check the HTTP Response code and display message if status code is not 200 (OK)
		switch($status_code[0]) {
			case 200:
				// Success
				break;
			case 503:
				die('Your call to OpenMeetings Web Services failed and returned an HTTP status of 503.   
			                     That means: Service unavailable. An internal problem prevented us from returning' . ' data to you.');
				break;
			case 403:
				die('Your call to OpenMeetings Web Services failed and returned an HTTP status of 403.   
			                     That means: Forbidden. You do not have permission to access this resource, or are over' . ' your rate limit.');
				break;
			case 400:
				// You may want to fall through here and read the specific XML error
				die('Your call to OpenMeetings Web Services failed and returned an HTTP status of 400.   
			                     That means:  Bad request. The parameters passed to the service did not match as expected.   
			                     The exact error is returned in the XML response.');
				break;
			default:
				die('Your call to OpenMeetings Web Services returned an unexpected HTTP status of: ' . $status_code[0] . " Request " . $request);
		}
		
		// Get the XML from the response, bypassing the header
		if (!($xml = strstr($response, '<ns'))) {
			$xml = null;
		}
		
		$dom = new DOMDocument();
		$dom->loadXML($xml);
		
		if ($returnAttribute == "") {
			//echo "XML".$xml."<br/>";
			return $this->getArray($dom);
		} else {
			$returnNodeList = $dom->getElementsByTagName($returnAttribute);
			$ret = array ();
			foreach ($returnNodeList as $returnNode) {
				if ($returnNodeList->length == 1) {
					return $this->getArray($returnNode);
				} else {
					$ret[] = $this->getArray($returnNode);
				}
			}
			return $ret;
		}
	}
	
	function getArray($node) {
		if (is_null($node) || !is_object($node)) {
			return $node;
		}
		$array = false;
		/*
			echo("!!!!!!!! NODE " . XML_TEXT_NODE
					. " :: name = " . $node->nodeName
					. " :: local = " . $node->localName
					. " :: childs ? " . $node->hasChildNodes()
					. " :: count = " . ($node->hasChildNodes() ? $node->childNodes->length : -1)
					. " :: type = " . $node->nodeType
					. " :: val = " . $node->nodeValue
					. "\n");

		if ($node->hasAttributes()) {
			foreach ($node->attributes as $attr) {
				$array[$attr->nodeName] = $attr->nodeValue;
			}
		}
		*/
		if ($node->hasChildNodes()) {
			foreach ($node->childNodes as $childNode) {
				if ($childNode->nodeType != XML_TEXT_NODE) {
					if ($node->hasAttributes()) {
						foreach ($node->attributes as $attr) {
							if ($attr->localName == "nil") {
								return null;
							}
						}
					}
					if ($childNode->childNodes->length == 1) {
						$array[$childNode->localName] = $this->getArray($childNode);
					} else {
						$array[$childNode->localName][] = $this->getArray($childNode);
					}
				} else {
					return $childNode->nodeValue;
					//echo("!!!!!!!! TEXT " . $childNode->nodeValue . "\n");
					//$array[$childNode->localName]
				}
			}
		}
		
		return $array;
	}
	
	function getError() {
		return false;
	}
	
	function fault() {
		return false;
	}
}
