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
 * Base class for all email notification regarding healthtest for Middlesex's Dorm and Student Functions Plugin.
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

abstract class healthtest_notification extends \local_mxschool\notification {

    /**
     * @param string $emailclass The class of the email as specified in the local_mxschool_notification database table.
     * @param int $id The healthtest id of the user who has submitted.
     *            The default value of 0 indicates a template email that should not be sent.
     * @throws coding_exception If the specified record does not exist.
     */
    public function __construct($emailclass, $htid) {
        global $DB;
        parent::__construct($emailclass);

        if ($htid) {
            $record = $DB->get_record_sql(
                "SELECT ht.id AS htid, ht.userid AS userid, u.firstname, u.lastname, u.alternatename, tb.id AS tbid, tb.testing_cycle,
			         tb.start_time, tb.end_time, tb.date
		       FROM {local_mxschool_healthtest} ht
			  LEFT JOIN {user} u ON u.id = ht.userid
			  LEFT JOIN {local_mxschool_testing_block} tb ON tb.id = ht.testing_block_id
			  WHERE ht.id = '{$htid}'"
            );
            if (!$record) {
                throw new \coding_exception("Record with id {$id} not found.");
            }

		  $this->data['firstname'] = $record->firstname;
		  $this->data['lastname'] = $record->lastname;
		  $this->data['alternatename'] = $record->alternatename;
  		  $this->data['test_block_start_time'] = date('g:i A', strtotime($record->start_time));
		  $this->data['test_block_end_time'] = date('g:i A', strtotime($record->end_time));
		  $this->data['test_block_date'] = date('F d', strtotime($record->date));

		  $cycle_dates = get_testing_cycle_dates($record->testing_cycle);
		  $this->data['testing_cycle_dates'] = date('F d', strtotime($cycle_dates['start'])).' -- '.date('F d', strtotime($cycle_dates['end']));

		  echo "<script>alert(USERID: {$record->userid})</script>";
		  error_log("USERID: {$record->userid}");

            array_push(
                $this->recipients, $DB->get_record('user', array('id' => $record->userid))
            );
        }
    }

    /**
     * @return array The list of strings which can serve as tags for the notification.
     */
    public function get_tags() {
        return array_merge(parent::get_tags(), array(
            'firstname', 'lastname', 'alternatename', 'test_block_start_time', 'test_block_end_time', 'test_block_date', 'testing_cycle_dates'
        ));
    }

}
