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
 * Email notification to the healthcenter for when a healthform is denied for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  healthpass
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\healthpass;

defined('MOODLE_INTERNAL') || die();

class healthcenter_notification extends \local_mxschool\notification {

	/**
      * @param int $id The id of the user who has submitted.
      *            The default value of 0 indicates a template email that should not be sent.
      * @throws coding_exception If the specified record does not exist.
      */
     public function __construct($id=0) {
         global $DB;
         parent::__construct('healthcenter_notification');

         if ($id) {
             $record = $DB->get_record_sql(
                 "SELECT u.id AS userid, u.firstname, u.lastname, u.alternatename,
			  		hp.symptoms, hp.body_temperature, s.boarding_status, s.phone_number, d.name AS dormname
                  FROM {user} u LEFT JOIN {local_mxschool_healthpass} hp ON u.id = hp.userid
			   			  LEFT JOIN {local_mxschool_student} s ON u.id = s.userid
			   	   		  LEFT JOIN {local_mxschool_dorm} d ON s.dormid = d.id
                  WHERE u.id = {$id}"
             );
             if (!$record) {
                 throw new \coding_exception("Record with id {$id} not found.");
             }

            $this->data['firstname'] = $record->firstname;
            $this->data['lastname'] = $record->lastname;
            $this->data['alternatename'] = $record->alternatename;
 		  $this->data['symptoms'] = $record->symptoms;
 		  $this->data['student_boarding_status'] = $record->boarding_status;
		  $this->data['student_phone_number'] = $record->phone_number;
   		  $this->data['student_dorm'] = $record->dormname;
		  $this->data['body_temperature'] = $record->body_temperature;

		  $healthcenter = $DB->get_record('user', array('id' => 2));
		  $healthcenter->email = get_config('local_mxschool', 'healthpass_notification_email_address');
		  $healthcenter->addresseename = 'Health Center';
		  $healthcenter->firstname = 'Health';
		  $healthcenter->lastname = 'Center';

             array_push(
                 $this->recipients, $healthcenter
             );
         }
     }

     /**
      * @return array The list of strings which can serve as tags for the notification.
      */
     public function get_tags() {
         return array_merge(parent::get_tags(), array(
             'firstname', 'lastname', 'alternatename', 'student_boarding_status', 'student_phone_number', 'student_dorm', 'symptoms', 'body_temperature'
         ));
     }
}
