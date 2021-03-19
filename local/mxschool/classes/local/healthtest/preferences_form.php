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
 * Preferences Form for Middlesex Health Testing Plugin.
 *
 * @package     local_mxschool
 * @subpackage  healthtest
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace local_mxschool\local\healthtest;

 defined('MOODLE_INTERNAL') || die();

 use local_mxschool\local\healthtest\healthtest_reminder;
 use local_mxschool\local\healthtest\healthtest_missed;
 use local_mxschool\local\healthtest\healthtest_confirm;

 class preferences_form extends \local_mxschool\form {

	 /**
	  * Form definition.
	  */
	 protected function definition() {
		 // Define fields
		 $fields = array(
			 'preferences' => array(
				 'healthtest_enabled' => array('element' => 'checkbox'),
				 'form_instructions' => self::ELEMENT_LONG_TEXT_REQUIRED,
			 ),
			 'reminder_notification' => array(
				 // 'reminder_enabled' => array('element' => 'checkbox'),
				 'reminder_enabled' => array('element' => 'static', 'text' => 'Auto reminders enabled'),
				 // 'reminder_time' => self::time_selector(1),
				 'reminder_time' => array('element' => 'static', 'text' => 'Auto reminders set for 6 PM each day.'),
				 'reminder_tags' => self::email_tags(new healthtest_reminder()),
				 'reminder_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
				 'reminder_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
			 ),
			 'missed_notification' => array(
				'missed_copy_healthcenter_enabled' => array('element' => 'checkbox'),
				'missed_tags' => self::email_tags(new healthtest_missed()),
				'missed_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
				'missed_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
                ),
			 'confirm_notification' => array(
				 'confirm_enabled' => array('element' => 'checkbox'),
				 'confirm_tags' => self::email_tags(new healthtest_confirm()),
				 'confirm_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
				 'confirm_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
			 )
		 );
		 $this->set_fields($fields, 'healthtest:preferences');
      }

	 /**
	 * Validates the preferences form before it can be submitted. Ensures max_body_temp is an integer
	 *
	 * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
	 */
	 public function validation($data, $files) {
	    global $DB;
	    $errors = parent::validation($data, $files);
	    return $errors;
	 }
}
