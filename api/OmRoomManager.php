<?php
require_once ("OmGateway.php");
class OmRoomManager {
	var $config = array();

	function __construct($cfg) {
		$this->config = $cfg;
	}
	
	function update($data) {
		$gateway = new OmGateway($this->config);
		if ($gateway->login()) {
			return $gateway->updateRoom($data);
		} else {
			echo "Could not login User to OpenMeetings, check your OpenMeetings Module Configuration";
		}
	}
	
	function delete($roomId) {
		$gateway = new OmGateway($this->config);
		if ($gateway->login()) {
			return $gateway->deleteRoom($roomId);
		} else {
			echo "Could not login User to OpenMeetings, check your OpenMeetings Module Configuration";
		}
	}
}
