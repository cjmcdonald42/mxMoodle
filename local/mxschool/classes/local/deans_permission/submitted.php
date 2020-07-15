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
 * Email notification for when a deans permission form is submitted for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  deans_permission
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\deans_permission;

defined('MOODLE_INTERNAL') || die();

class submitted extends \local_mxschool\notification {

    /**
     * @param int $id The id of the deans permission form which has been submitted.
     *                The default value of 0 indicates a template email that should not be sent.
     * @throws coding_exception If the specified record does not exist.
     */
    public function __construct($id = 0) {
        global $DB;
        parent::__construct('deans_permission_submitted');

        if ($id) {
            $record = $DB->get_record_sql(
                "SELECT dp.id, CONCAT(u.firstname, ' ', u.lastname) AS fullname, su.grade, su.boarding_status, dp.event,
			 	    dp.sport, dp.departure_time, dp.return_time
			  FROM {local_mxschool_deans_perm} dp LEFT JOIN {user} u ON dp.userid = u.id
			  							   LEFT JOIN {local_mxschool_student} su ON dp.userid = su.userid
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
		  $this->data['departure_time'] = format_date('n/j/y g:i A', $record->departure_time);
		  $this->data['return_time'] = format_date('n/j/y g:i A', $record->return_time);

		  $deans = $DB->get_record('user', array('id' => 2));
		  $deans->email = get_config('local_mxschool', 'deans_email_address');
		  $deans->firstname = 'Deans';
		  $deans->lastname = 'Deans';

            array_push(
                $this->recipients, $deans
            );
        }
    }

    /**
     * @return array The list of strings which can serve as tags for the notification.
     */
    public function get_tags() {
        return array_merge(parent::get_tags(), array(
            'fullname', 'grade', 'boarding_status', 'event', 'sport', 'departure_time', 'return_time'
        ));
    }
}
