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
 * Vacation travel table for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage vacation_travel
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class vacation_table extends local_mxschool_table {

    /**
     * Creates a new vacation_table.
     *
     * @param stdClass $filter Any filtering for the table - could include properties dorm, submitted, and search
     */
    public function __construct($filter) {
        $columns = array('student', 'dorm', 'destination', 'phone', 'depdatetime', 'deptype');
        if (get_config('local_mxschool', 'vacation_form_returnenabled')) {
            $columns = array_merge($columns, array('retdatetime', 'rettype', 'retinfo'));
        }
        if ($filter->dorm !== '') {
            unset($columns[array_search('dorm', $columns)]);
        }
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("vacation_travel_report_header_{$column}", 'local_mxschool');
        }
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $fields = array(
            's.id', 'u.id AS userid', 't.id AS tid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname',
            'u.alternatename', 'd.name AS dorm', 't.destination', 't.phone_number AS phone',
            'dt.date_time AS depdatetime', 'dt.type AS deptype', 'rt.date_time AS retdatetime', 'rt.type AS rettype',
            'rt.carrier AS retcarrier', 'rt.transportation_number AS retnumber', "'' AS retinfo"
        );
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_dorm} d ON s.dormid = d.id',
            '{local_mxschool_vt_trip} t ON s.userid = t.userid', '{local_mxschool_vt_transport} dt ON t.departureid = dt.id',
            '{local_mxschool_vt_transport} rt ON t.returnid = rt.id'
        );
        $where = array(
            'u.deleted = 0', "s.boarding_status = 'Boarder'", $filter->dorm ? "s.dormid = {$filter->dorm}" : '',
            $filter->submitted === '1' ? "EXISTS (SELECT userid FROM {local_mxschool_vt_trip} WHERE userid = u.id)" : (
                $filter->submitted === '0'
                ? "NOT EXISTS (SELECT userid FROM {local_mxschool_vt_trip} WHERE userid = u.id)" : ''
            )
        );
        $sortable = array(
            'student', 'dorm', 'destination', 'depdatetime', 'deptype', 'retdatetime', 'rettype'
        );
        $urlparams = array(
            'dorm' => $filter->dorm, 'submitted' => $filter->submitted, 'search' => $filter->search
        );
        $centered = array('depdatetime', 'deptype', 'retdatetime', 'rettype', 'retinfo');
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 't.destination');
        parent::__construct(
            'vaction_table', $columns, $headers, $sortable, 'student', $fields, $from, $where, $urlparams, $centered,
            $filter->search, $searchable
        );
    }

    /**
     * Formats the departure date and time column to 'n/j/y g:i A'.
     */
    protected function col_depdatetime($values) {
        return $values->depdatetime ? date('n/j/y g:i A', $values->depdatetime) : '';
    }

    /**
     * Formats the return date and time column to 'n/j/y g:i A'.
     */
    protected function col_retdatetime($values) {
        return $values->tid ? ($values->retdatetime ? date('n/j/y g:i A', $values->retdatetime) : '-') : '';
    }

    /**
     * Formats the return type column.
     */
    protected function col_rettype($values) {
        return $values->tid ? ($values->rettype ?: '-') : '';
    }

    /**
     * Formats the return info column.
     */
    protected function col_retinfo($values) {
        return $values->tid ? ("{$values->retcarrier} &ndash; {$values->retnumber}" ?: '-') : '';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return isset($values->tid) ? $this->edit_icon('/local/mxschool/vacation_travel/vacation_enter.php', $values->tid) : '';
    }

}
