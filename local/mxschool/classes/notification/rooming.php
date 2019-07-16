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
 * Email notifications for the rooming subpackage of Middlesex's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage rooming
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\rooming;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/mx_notification.php');

use \local_mxschool\local\notification;
use \local_mxschool\local\bulk_notification;

/**
 * Email notification for when a rooming form is submitted for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage rooming
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class submitted extends notification {

    /**
     * @param int $id The id of the rooming form which has been submitted.
     *                The default value of 0 indicates a template email that should not be sent.
     * @throws coding_exception If the specified record does not exist.
     */
    public function __construct($id = 0) {
        global $DB;
        parent::__construct('rooming_submitted');

        if ($id) {
            $record = $DB->get_record_sql(
                "SELECT r.userid AS student, r.has_lived_in_double AS haslivedindouble, r.room_type AS roomtype,
                        r.dormmate1id AS dormmate1, r.dormmate2id AS dormmate2, r.dormmate3id AS dormmate3,
                        r.dormmate4id AS dormmate4, r.dormmate5id AS dormmate5, r.dormmate6id AS dormmate6,
                        r.preferred_roommateid AS preferredroommate, r.time_modified AS timesubmitted
                 FROM {local_mxschool_rooming} r LEFT JOIN {local_mxschool_student} s on r.userid = s.userid
                 WHERE r.id = ?", array($id)
            );
            if (!$record) {
                throw new \coding_exception("Record with id {$id} not found.");
            }

            $this->data['haslivedindouble'] = format_boolean($record->haslivedindouble);
            $this->data['roomtype'] = $record->roomtype;
            $this->data['dormmate1'] = format_student_name($record->dormmate1);
            $this->data['dormmate2'] = format_student_name($record->dormmate2);
            $this->data['dormmate3'] = format_student_name($record->dormmate3);
            $this->data['dormmate4'] = format_student_name($record->dormmate4);
            $this->data['dormmate5'] = format_student_name($record->dormmate5);
            $this->data['dormmate6'] = format_student_name($record->dormmate6);
            $this->data['preferredroommate'] = format_student_name($record->preferredroommate);
            $this->data['timesubmitted'] = format_date('n/j/y g:i A', $record->timesubmitted);

            $this->recipients[] = $DB->get_record('user', array('id' => $record->student));
        }
    }

    /**
     * @return array The list of strings which can serve as tags for the notification.
     */
    public function get_tags() {
        return array_merge(parent::get_tags(), array(
            'haslivedindouble', 'roomtype', 'dormmate1', 'dormmate2', 'dormmate3', 'dormmate4', 'dormmate5', 'dormmate6',
            'preferredroommate', 'timesubmitted'
        ));
    }

}

/**
 * Email notification to remind students to complete the rooming form
 * for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage rooming
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unsubmitted_notification extends notification {

    /**
     * @param int $id The userid of the recipient. A value of 0 indicates that the notification should be sent to the deans.
     */
    public function __construct($id = 0) {
        global $DB;
        parent::__construct('rooming_notify_unsubmitted');

        $this->recipients[] = $id ? $DB->get_record('user', array('id' => $id)) : self::get_deans_user();
    }

}

/**
 * Bulk wrapper for the the unsubmitted_notification for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage rooming
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notify_unsubmitted extends bulk_notification {

    public function __construct() {
        $list = get_student_without_rooming_form_list();
        foreach ($list as $userid => $name) {
            $this->notifications[] = new unsubmitted_notification($userid);
        }
        $this->notifications[] = new unsubmitted_notification();
    }

}
