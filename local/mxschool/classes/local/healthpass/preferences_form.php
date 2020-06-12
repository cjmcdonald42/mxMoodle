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

/**
 * Preferences Form for Middlesex Health Pass Plugin.
 *
 * @package     local_mxschool
 * @subpackage  healthpass
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace local_mxschool\local\healthpass;

 defined('MOODLE_INTERNAL') || die();

 class preferences_form extends \local_mxschool\form {

	 /**
	  * Form definition.
	  */
	 protected function definition() {
		 $fields = array(
			 'preferences' => array(
				 'reset_time' => self::time_selector(1),
				 'healthpass_enabled' => array('element' => 'checkbox')
			 ),
			 'podio_info' => array(
				 'client_id' => self::ELEMENT_LONG_TEXT,
				 'client_secret' => self::ELEMENT_LONG_TEXT,
				 'app_id' => self::ELEMENT_LONG_TEXT,
				 'app_token' => self::ELEMENT_LONG_TEXT,
			 )
		 );
		 $this->set_fields($fields, 'healthpass:preferences');
      }
}
