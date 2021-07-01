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
 * Vacation travel table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  vacation_travel
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\vacation_travel;

defined('MOODLE_INTERNAL') || die();

class table extends \local_mxschool\table {

    /**
     * Creates a new vacation_table.
     *
     * @param stdClass $filter Any filtering for the table - could include properties dorm, submitted, and search.
     */
    public function __construct($filter) {
        $columns = array('student', 'dorm', 'destination', 'phone', 'depdatetime', 'deptype', 'retdatetime', 'rettype', 'retinfo');
        if ($filter->dorm) {
            unset($columns[array_search('dorm', $columns)]);
        }
	   if (!get_config('local_mxschool', 'vacation_form_departureenabled')) {
            unset($columns[array_search('depdatetime', $columns)]);
            unset($columns[array_search('deptype', $columns)]);
        }
        if (!get_config('local_mxschool', 'vacation_form_returnenabled')) {
            unset($columns[array_search('retdatetime', $columns)]);
            unset($columns[array_search('rettype', $columns)]);
            unset($columns[array_search('retinfo', $columns)]);
        }
        $headers = $this->generate_headers($columns, 'vacation_travel:report');
        $sortable = array('student', 'destination', 'depdatetime', 'deptype', 'retdatetime', 'rettype');
        $centered = array('depdatetime', 'deptype', 'retdatetime', 'rettype', 'retinfo');
        parent::__construct('vaction_table', $columns, $headers, $sortable, $centered, $filter);

        $fields = array(
            's.id', 's.userid', 't.id AS tid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 's.dormid',
            't.destination', 't.phone_number AS phone', 'dt.date_time AS depdatetime', 'dt.type AS deptype',
            'rt.date_time AS retdatetime', 'rt.type AS rettype', 'rt.carrier AS retcarrier',
            'rt.transportation_number AS retnumber',
        );
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_vt_trip} t ON s.userid = t.userid',
            '{local_mxschool_vt_transport} dt ON t.departureid = dt.id', '{local_mxschool_vt_transport} rt ON t.returnid = rt.id'
        );
        $where = array('u.deleted = 0', "s.boarding_status = 'Boarder'");
        if ($filter->dorm) {
            $where[] = "s.dormid = {$filter->dorm}";
        }
        switch ($filter->submitted) {
            case '1':
                $where[] = "EXISTS (SELECT userid FROM {local_mxschool_vt_trip} WHERE userid = u.id)";
                break;
            case '0':
                $where[] = "NOT EXISTS (SELECT userid FROM {local_mxschool_vt_trip} WHERE userid = u.id)";
                break;
        }
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 't.destination');
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
    }

    /**
     * Formats the departure date and time column to 'n/j/y g:i A'.
     */
    protected function col_depdatetime($values) {
        return $values->depdatetime ? format_date('n/j/y g:i A', $values->depdatetime) : '';
    }

    /**
     * Formats the return date and time column to 'n/j/y g:i A'.
     */
    protected function col_retdatetime($values) {
        return $values->tid ? ($values->retdatetime ? format_date('n/j/y g:i A', $values->retdatetime) : '-') : '';
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
        return isset($values->tid) ? $this->edit_icon('/local/mxschool/vacation_travel/form.php', $values->tid) : '';
    }

}
