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
 * Weekend checkin sheet table for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class weekend_table extends local_mxschool_table {

    /**
     * Creates a new weekend_table.
     *
     * @param string $uniqueid a unique identifier for the table.
     * @param stdClass $filter any filtering for the table - could include dorm, weekend, submitted, and search keys.
     */
    public function __construct($uniqueid, $filter) {
        global $DB;
        $columns = array('student', 'room', 'grade');
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("weekend_report_header_{$column}", 'local_mxschool');
        }
        $fields = array(
            's.id', 'wf.id AS wfid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname', 'u.alternatename', 's.room',
            's.grade', "'&emsp;' AS clean", 'wf.parent', 'wf.invite', 'wf.approved', 'wf.destination', 'wf.transportation',
            'wf.phone_number AS phone', 'wf.departure_date_time AS departuretime', 'wf.return_date_time AS returntime'
        );
        $weekendrecord = $DB->get_record('local_mxschool_weekend', array('id' => $filter->weekend), 'start_time, end_time');
        $startday = date('w', $weekendrecord->start_time) - 7;
        $endday = date('w', $weekendrecord->end_time);
        for ($i = 1; $i <= $endday - $startday + 1; $i++) {
            $columns[] = "early_$i";
            $headers[] = get_string('weekend_report_header_early', 'local_mxschool');
            $fields[] = "'&emsp;' AS early_$i";
            $columns[] = "late_$i";
            $headers[] = get_string('weekend_report_header_late', 'local_mxschool');
            $fields[] = "'&emsp;' AS late_$i";
        }
        $columns2 = array(
            'clean', 'parent', 'invite', 'approved', 'destination', 'transportation', 'phone', 'departuretime', 'returntime'
        );
        $headers2 = array();
        foreach ($columns2 as $column) {
            $headers2[] = get_string("weekend_report_header_{$column}", 'local_mxschool');
        }
        $columns = array_merge($columns, $columns2);
        $headers = array_merge($headers, $headers2);
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id',
            "{local_mxschool_weekend_form} wf ON s.userid = wf.userid AND wf.weekendid = $filter->weekend AND wf.active = 1"
        );
        $where = array(
            'u.deleted = 0', $filter->dorm ? "s.dormid = $filter->dorm" : '', $filter->submitted === '1' ? "EXISTS (
                SELECT userid FROM mdl_local_mxschool_weekend_form wf WHERE s.userid = userid AND wf.weekendid = $filter->weekend
            )" : '', $filter->submitted === '0' ? "NOT EXISTS (
                SELECT userid FROM mdl_local_mxschool_weekend_form wf WHERE s.userid = userid AND wf.weekendid = $filter->weekend
            )" : ''
        );
        $sortable = array('student', 'room', 'grade', 'destination', 'transportation');
        $urlparams = array(
            'dorm' => $filter->dorm, 'weekend' => $filter->weekend, 'submitted' => $filter->submitted, 'search' => $filter->search
        );
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'wf.destination', 'wf.transportation');
        $noprint = array('actions');
        parent::__construct(
            $uniqueid, $columns, $headers, $sortable, 'student', $fields, $from, $where, $urlparams, $filter->search, $searchable,
            $noprint
        );
    }

    /**
     * Formats the student column to "last, first (alternate)" or "last, first".
     */
    protected function col_student($values) {
        $alternatename = $values->alternatename && $values->alternatename !== $values->firstname ? " ($values->alternatename)" : '';
        return $values->student . $alternatename;
    }

    /**
     * Formats the departure time column to 'n/j/y g:i A'.
     */
    protected function col_departuretime($values) {
        return $values->departuretime ? date('n/j/y g:i A', $values->departuretime) : '';
    }

    /**
     * Formats the return time column to 'n/j/y g:i A'.
     */
    protected function col_returntime($values) {
        return $values->returntime ? date('n/j/y g:i A', $values->returntime) : '';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/mxschool/checkin/weekend_enter.php', $values->wfid)
              .$this->delete_icon($values->wfid);
    }

}
