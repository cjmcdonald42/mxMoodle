<?php

// PHP Client for the Podio api http://podio.com/
// @author Podio Community

defined('MOODLE_INTERNAL') || die();

class PodioResponse {
	public $body;
	public $status;
	public $headers;
	public function json_body() {
		return json_decode($this->body, TRUE);
	}
	public function __toString() {
		return $body."\n".$status."\n".$headers;
	}
}
