Template for Tables
Header
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
        $columns = array(
            'student', 'room', 'grade', 'early', 'late', 'clean', 'parent', 'invite', 'approval', 'destination', 'transportation',
            'phone', 'departuretime', 'returntime'
        );
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("weekend_report_header_{$column}", 'local_mxschool');
        }
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $fields = array(
            's.id', 'wf.weekendid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname', 'u.alternatename', 's.room',
            's.grade', "'&emsp;' AS early", "'&emsp;' AS late", "'&emsp;' AS clean", "'&emsp;' AS parent", "'&emsp;' AS invite",
            "'&emsp;' AS approval", 'wf.destination', 'wf.transportation', 'wf.phone_number AS phone',
            'wf.departure_date_time AS departuretime', 'wf.return_date_time AS returntime'
        );
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_weekend_form} wf ON s.userid = wf.userid'
        );
        $where = array('u.deleted = 0', "wf.weekendid = $filter->weekend", $filter->dorm ? "s.dormid = $filter->dorm" : ''); // TODO: check for multiple reports.
        $sortable = array('student', 'room', 'grade', 'destination', 'transportation');
        $urlparams = array(
            'dorm' => $filter->dorm, 'weekend' => $filter->weekend, 'submitted' => $filter->submitted, 'search' => $filter->search
        );
        $searchable = array('student', 'destination', 'transportation');
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
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/mxschool/checkin/weekend_enter.php', $values->weekendid)
              .$this->delete_icon($values->weekendid);
    }

}
