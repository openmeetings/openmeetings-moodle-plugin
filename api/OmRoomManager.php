<?php
require_once("OmGateway.php");
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
            return -1;
        }
    }

    function delete($roomId) {
        $gateway = new OmGateway($this->config);
        if ($gateway->login()) {
            return $gateway->deleteRoom($roomId);
        } else {
            return -1;
        }
    }

    function get($roomId) {
        $gateway = new OmGateway($this->config);
        if ($gateway->login()) {
            return $gateway->getRoom($roomId);
        } else {
            return -1;
        }
    }
}
