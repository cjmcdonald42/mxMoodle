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
 * Form for editing deans permission preferences for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  deans_permission
 * @author      mxMoodle Development Team
 * @copyright   2022 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\deans_permission;

defined('MOODLE_INTERNAL') || die();

use local_mxschool\local\deans_permission\submitted;
use local_mxschool\local\deans_permission\sports_permission_request;
// Athletic Review and Academic Review consolidated into one notification so remove separate notification
// use local_mxschool\local\deans_permission\class_permission_request;

// TODO What happened to the healthcenter notification?
// Restore this line but I can't find where it was deleted - so I'm writing it now.
use local_mxschool\local\deans_permission\notify_healthcenter;
use local_mxschool\local\deans_permission\deans_permission_notify_student;
use local_mxschool\local\deans_permission\deans_permission_approved;
// TODO The deans_permission_denied.php form exists but is missing and entry here.
// Is that a mistake?

class preferences_form extends \local_mxschool\form {

    /**
     * Form definition.
     */
    protected function definition() {
        $fields = array(
		  'deans_email' => array(
			   'deans_email_address' => self::ELEMENT_EMAIL_REQUIRED,
			   'submitted_tags' => self::email_tags(new submitted()),
			   'submitted_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
			   'submitted_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
		  ),
		  'review_email' => array(
			  'athletic_director_email_address' => self::ELEMENT_EMAIL_REQUIRED,
			  'academic_director_email_address' => self::ELEMENT_EMAIL_REQUIRED,
			  'review_tags' => self::email_tags(new sports_permission_request()),
			  'review_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
			  'review_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
		  ),

// TODO restore the Healthcenter Notificaion email
// TODO Change the lang here to notify_healthcenter_email
          'notify_email' => array(
 			  'healthcenter_email_address' => self::ELEMENT_EMAIL_REQUIRED,
 			  'notify_tags' => self::email_tags(new class_permission_request()),
 			  'notify_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
 			  'notify_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
 		  ),

          'notify_student_email' => array(
			  'info' => array('element' => 'static', 'text' => get_string('deans_permission:preferences:notify_student_email:note', 'local_mxschool')),
			  'notify_student_tags' => self::email_tags(new notify_student()),
			  'notify_student_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
			  'notify_student_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
		  ),
		  'approved_email' => array(
			  'info' => array('element' => 'static', 'text' => get_string('deans_permission:preferences:approved_email:note', 'local_mxschool')),
			  'approved_tags' => self::email_tags(new deans_permission_approved()),
			  'approved_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
			  'approved_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
		  ),
		  'denied_email' => array(
			  'denied_tags' => self::email_tags(new deans_permission_approved()),
			  'denied_subject' => self::ELEMENT_LONG_TEXT_REQUIRED,
			  'denied_body' => self::ELEMENT_FORMATTED_TEXT_REQUIRED
		  )
        );
        $this->set_fields($fields, 'deans_permission:preferences', true);
    }
}
