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
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 namespace local_mxschool\local\healthpass;

 defined('MOODLE_INTERNAL') || die();

 use local_mxschool\local\healthpass\healthcenter_notification;
 use local_mxschool\local\healthpass\healthpass_approved;
 use local_mxschool\local\healthpass\healthpass_denied;
 use local_mxschool\local\healthpass\healthpass_overridden;
 use local_mxschool\local\healthpass\unsubmitted;

 class preferences_form extends \local_mxschool\form {

	 /**
	  * Form definition.
	  */
	 protected function definition() {
		 // Define fields
		 $fields = array(
			 'preferences' => array(
				 'reset_time' => self::time_selector(1),
				 'max_body_temp' => self::ELEMENT_TEXT,
				 'healthpass_enabled' => array('element' => 'checkbox')
			 ),
			 'healthcenter_notification' => array(
				 'healthcenter_notification_enabled' => array('element' => 'checkbox'),
				 'healthcenter_email_address' => self::ELEMENT_LONG_TEXT_REQUIRED,
				 'healthcenter_tags' => self::email_tags(new healthcenter_notification()),
				 'healthcenter_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
				 'healthcenter_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
			 ),
			 'submitted_notifications' => array(
                    'approved_tags' => self::email_tags(new healthpass_approved()),
                    'approved_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
                    'approved_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
				'denied_tags' => self::email_tags(new healthpass_denied()),
				'denied_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
				'denied_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED,
				'overridden_tags' => self::email_tags(new healthpass_overridden()),
				'overridden_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
				'overridden_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
                ),
			 'unsubmitted_notifications' => array(
				 'days_before_reminder' =>self::ELEMENT_TEXT,
				 'unsubmitted_tags' => self::email_tags(new unsubmitted()),
				 'unsubmitted_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
				 'unsubmitted_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
			 )
		 );
		 $this->set_fields($fields, 'healthpass:preferences');
      }

	 /**
	 * Validates the preferences form before it can be submitted. Ensures max_body_temp is an integer
	 *
	 * @return array of errors as "element_name"=>"error_description" or an empty array if there are no errors.
	 */
	 public function validation($data, $files) {
	    global $DB;
	    $errors = parent::validation($data, $files);
	    if(!is_numeric($data['max_body_temp'])) $errors['max_body_temp'] = get_string('healthpass:preferences:error:not_numeric', 'local_mxschool');
	    if(!is_numeric($data['days_before_reminder'])) $errors['days_before_reminder'] = get_string('healthpass:preferences:error:not_numeric', 'local_mxschool');
	    return $errors;
	 }
}
