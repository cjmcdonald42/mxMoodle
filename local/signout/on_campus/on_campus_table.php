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
 * On-campus signout table for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  on_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../../mxschool/classes/mx_table.php');
require_once(__DIR__.'/../classes/output/renderable.php');

class on_campus_table extends local_mxschool_table {

    /**
     * Creates a new on_campus_table.
     *
     * @param stdClass $filter any filtering for the table - could include properties dorm, location, date, and search.
     */
    public function __construct($filter) {
        global $USER;
        $columns = array('student', 'grade', 'dorm', 'location', 'signoutdate', 'signouttime', 'confirmation', 'signin');
        if ($filter->dorm) {
            unset($columns[array_search('dorm', $columns)]);
        }
        if ($filter->location > 0) {
            unset($columns[array_search('location', $columns)]);
        }
        if ($filter->date) {
            unset($columns[array_search('signoutdate', $columns)]);
        }
        $headers = $this->generate_headers($columns, 'on_campus_report', 'local_signout');
        $sortable = array($filter->date ? 'signouttime' : 'signoutdate', 'student', 'grade', 'dorm', 'location');
        $centered = array('grade', 'signoutdate', 'signouttime', 'confirmation', 'signin');
        parent::__construct('on_campus_table', $columns, $headers, $sortable, $centered, $filter, true, false);
        $this->add_column_class('signin', 'sign-in');

        $fields = array(
            'oc.id', 'oc.userid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 's.grade', 'd.name AS dorm',
            'l.name AS location', 'oc.other', 'oc.time_created AS signoutdate', 'oc.time_created AS signouttime',
            'oc.confirmerid AS confirmer', 'oc.confirmation_time AS confirmationtime', 'oc.sign_in_time AS signin'
        );
        $from = array(
            '{local_signout_on_campus} oc', '{user} u ON oc.userid = u.id', '{local_mxschool_student} s ON s.userid = u.id',
            '{local_mxschool_dorm} d ON s.dormid = d.id', '{local_signout_location} l ON oc.locationid = l.id',
            '{user} c ON oc.confirmerid = c.id'
        );
        $where = array('oc.deleted = 0', 'u.deleted = 0');
        if ($filter->dorm) {
            switch ($filter->dorm) {
                case -2:
                    $where[] = 's.boarding_status = "Boarder"';
                    break;
                case -1:
                    $where[] = 's.boarding_status = "Day"';
                    break;
                default:
                    $where[] = "s.dormid = {$filter->dorm}";
            }
        }
        if ($filter->location) {
            $where[] = "oc.locationid = {$filter->location}";
        }
        if ($filter->date) {
            $starttime = generate_datetime($filter->date);
            $endtime = clone $starttime;
            $endtime->modify('+1 day');
            array_push($where, "oc.time_created >= {$starttime->getTimestamp()}", "oc.time_created < {$endtime->getTimestamp()}");
        }
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'l.name', 'oc.other', 'c.firstname', 'c.lastname');
        $this->set_sql($fields, $from, $where, $searchable, $filter->search);
    }

    /**
     * Formats the student column to "last, first (preferred)" or "last, first".
     */
    protected function col_student($values) {
        return format_student_name($values->userid);
    }

    /**
     * Formats the location column.
     */
    protected function col_location($values) {
        return $values->other ?? $values->location;
    }

    /**
     * Formats the sign out date column to 'n/j/y'.
     */
    protected function col_signoutdate($values) {
        return format_date('n/j/y', $values->signoutdate);
    }

    /**
     * Formats the sign out time time column to 'g:i A'.
     */
    protected function col_signouttime($values) {
        return format_date('g:i A', $values->signouttime);
    }

    /**
     * Formats the confirmation column.
     */
    protected function col_confirmation($values) {
        if (!isset($values->confirmationtime)) {
            return '-';
        }
        return get_string('on_campus_report_column_confirmation_text', 'local_signout', array(
            'confirmer' => format_faculty_name($values->confirmer),
            'confirmationtime' => format_date('g:i A', $values->confirmationtime)
        ));
    }

    /**
     * Formats the sign-in time column to 'g:i A'.
     */
    protected function col_signin($values) {
        return isset($values->signin) ? format_date('g:i A', $values->signin) : '-';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/signout/on_campus/on_campus_enter.php', $values->id) . $this->delete_icon($values->id);
    }

}
