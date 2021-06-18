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
 * Base class for all email notification regarding deans permission form
 *
 * @package     local_mxschool
 * @subpackage  deans_permission
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\deans_permission;

defined('MOODLE_INTERNAL') || die();

abstract class deans_permission_notification extends \local_mxschool\notification {

    /**
     * @param string $emailclass The class of the email as specified in the local_mxschool_notification database table.
     * @param int $id The id of the weekend form which has been submitted.
     *            The default value of 0 indicates a template email that should not be sent.
     * @throws coding_exception If the specified record does not exist.
     */
    public function __construct($emailclass, $id=0) {
	    global $DB;
         parent::__construct($emailclass);

         if ($id) {
             $record = $DB->get_record_sql(
                 "SELECT dp.id, CONCAT(u.firstname, ' ', u.lastname) AS fullname, su.grade, su.boarding_status, dpe.name AS event,
 			 	    dp.sport, dp.times_away, dp.event_date, dp.external_comment, dp.event_info
 			  FROM {local_mxschool_deans_perm} dp LEFT JOIN {user} u ON dp.userid = u.id
 			  							   LEFT JOIN {local_mxschool_student} su ON dp.userid = su.userid
										   LEFT JOIN {local_mxschool_dp_event} dpe ON dp.event_id = dpe.id
 			  WHERE dp.id = {$id}"
             );
             if (!$record) {
                 throw new \coding_exception("Record with id {$id} not found.");
             }

            $this->data['fullname'] = $record->fullname;
 		  $this->data['grade'] = $record->grade;
 		  $this->data['boarding_status'] = $record->boarding_status;
 		  $this->data['event'] = $record->event;
 		  $this->data['sport'] = $record->sport;
 		  $this->data['times_away'] = $record->times_away;
		  $this->data['event_date'] = $record->event_date;
		  $this->data['message_to_student'] = $record->external_comment;
		  $this->data['event_info'] = $record->event_info;
         }
    }

     /**
      * @return array The list of strings which can serve as tags for the notification.
      */
     public function get_tags() {
         return array_merge(parent::get_tags(), array(
             'fullname', 'grade', 'boarding_status', 'event', 'event_info', 'event_date', 'sport', 'times_away', 'message_to_student'
         ));
     }

}
