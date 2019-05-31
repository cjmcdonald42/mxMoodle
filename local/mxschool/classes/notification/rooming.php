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
 * Email notifications for the rooming subpackage of Middlesex School's Dorm and Student functions plugin.
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

require_once('mx_notification.php');

use local_mxschool\local\notification;

/**
 * Email notification for when a rooming form is submitted for Middlesex School's Dorm and Student functions plugin.
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
     * @throws coding_exception If the specified record does not exist.
     */
    public function __construct($id) {
        global $DB;
        parent::__construct('rooming_submitted');

        $record = $DB->get_record_sql(
            "SELECT r.userid AS student, r.has_lived_in_double AS haslivedindouble, r.room_type AS roomtype,
                    d1.firstname AS d1firstname, d1.lastname AS d1lastname, d1.alternatename AS d1alternatename,
                    d2.firstname AS d2firstname, d2.lastname AS d2lastname, d2.alternatename AS d2alternatename,
                    d3.firstname AS d3firstname, d3.lastname AS d3lastname, d3.alternatename AS d3alternatename,
                    d4.firstname AS d4firstname, d4.lastname AS d4lastname, d4.alternatename AS d4alternatename,
                    d5.firstname AS d5firstname, d5.lastname AS d5lastname, d5.alternatename AS d5alternatename,
                    d6.firstname AS d6firstname, d6.lastname AS d6lastname, d6.alternatename AS d6alternatename,
                    p.firstname AS pfirstname, p.lastname AS plastname, p.alternatename AS palternatename,
                    r.time_modified AS timesubmitted
             FROM {local_mxschool_rooming} r LEFT JOIN {local_mxschool_student} s on r.userid = s.userid
             LEFT JOIN {user} d1 ON r.dormmate1id = d1.id LEFT JOIN {user} d2 ON r.dormmate2id = d2.id
             LEFT JOIN {user} d3 ON r.dormmate3id = d3.id LEFT JOIN {user} d4 ON r.dormmate4id = d4.id
             LEFT JOIN {user} d5 ON r.dormmate5id = d5.id LEFT JOIN {user} d6 ON r.dormmate6id = d6.id
             LEFT JOIN {user} p ON r.preferred_roommateid = p.id WHERE r.id = ?", array($id)
        );
        if (!$record) {
            throw new coding_exception("Record with id {$id} not found.");
        }

        $this->data['haslivedindouble'] = boolean_to_yes_no($record->haslivedindouble);
        $this->data['roomtype'] = $record->roomtype;
        $this->data['dormmate1'] = "{$record->d1lastname}, {$record->d1firstname}" . (
            !empty($record->d1alternatename) && $record->d1alternatename !== $record->d1firstname
                ? " ({$record->d1alternatename})" : ''
        );
        $this->data['dormmate2'] = "{$record->d2lastname}, {$record->d2firstname}" . (
            !empty($record->d2alternatename) && $record->d2alternatename !== $record->d2firstname
                ? " ({$record->d2alternatename})" : ''
        );
        $this->data['dormmate3'] = "{$record->d3lastname}, {$record->d3firstname}" . (
            !empty($record->d3alternatename) && $record->d3alternatename !== $record->d3firstname
                ? " ({$record->d3alternatename})" : ''
        );
        $this->data['dormmate4'] = "{$record->d4lastname}, {$record->d4firstname}" . (
            !empty($record->d4alternatename) && $record->d4alternatename !== $record->d4firstname
                ? " ({$record->d4alternatename})" : ''
        );
        $this->data['dormmate5'] = "{$record->d5lastname}, {$record->d5firstname}" . (
            !empty($record->d5alternatename) && $record->d5alternatename !== $record->d5firstname
                ? " ({$record->d5alternatename})" : ''
        );
        $this->data['dormmate6'] = "{$record->d6lastname}, {$record->d6firstname}" . (
            !empty($record->d6alternatename) && $record->d6alternatename !== $record->d6firstname
                ? " ({$record->d6alternatename})" : ''
        );
        $this->data['preferredroomate'] = "{$record->plastname}, {$record->pfirstname}" . (
            !empty($record->palternatename) && $record->palternatename !== $record->pfirstname
                ? " ({$record->palternatename})" : ''
        );
        $this->data['timesubmitted'] = date('n/j/y g:i A', $record->timesubmitted);

        $this->recipients[] = $DB->get_record('user', array('id' => $record->student));
    }

    /**
     * @return array The list of strings which can serve as tags for the notification.
     */
    public function get_tags() {
        return array_merge(array(
            'haslivedindouble', 'roomtype', 'dormmate1', 'dormmate2', 'dormmate3', 'dormmate4', 'dormmate5', 'dormmate6',
            'preferredroomate', 'timesubmitted'
        ), parent::get_tags());
    }

}

/**
 * Email notification to remind students to complete the rooming form
 * for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage rooming
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class notify_unsubmitted extends notification {

    public function __construct() {
        global $DB;
        parent::__construct('rooming_notify_unsubmitted');

        $list = get_student_without_rooming_form_list();
        foreach ($list as $userid => $name) {
            $this->recipients[] = $DB->get_record('user', array('id' => $userid));
        }
        $this->recipients[] = self::get_deans_user();
    }

}
