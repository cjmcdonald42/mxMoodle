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
 * Rooming table for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class rooming_table extends local_mxschool_table {

    /**
     * Creates a new rooming_table.
     *
     * @param stdClass $filter any filtering for the table - could include submitted, gender, roomtype, double, and search.
     * @param string $download Indicates whether the table is downloading.
     */
    public function __construct($filter, $download) {
        $this->is_downloading($download, 'Rooming Requests', 'Rooming Requests');
        $columns = array('student', 'grade', 'gender', 'dorm', 'roomtype', 'dormmates', 'liveddouble', 'roommate');
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("rooming_report_header_{$column}", 'local_mxschool');
        }
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $fields = array(
            's.id', 'u.id AS userid', 'r.id AS rid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname',
            'u.alternatename', 's.grade', 's.gender', 'd.name AS dorm', 'r.room_type AS roomtype', "'' AS dormmates",
            "CONCAT(d1.lastname, ', ', d1.firstname) AS d1name", 'd1.firstname AS d1firstname',
            'd1.alternatename AS d1alternatename', "CONCAT(d2.lastname, ', ', d2.firstname) AS d2name",
            'd2.firstname AS d2firstname', 'd2.alternatename AS d2alternatename',
            "CONCAT(d3.lastname, ', ', d3.firstname) AS d3name", 'd3.firstname AS d3firstname',
            'd3.alternatename AS d3alternatename', "CONCAT(d4.lastname, ', ', d4.firstname) AS d4name",
            'd4.firstname AS d4firstname', 'd4.alternatename AS d4alternatename',
            "CONCAT(d5.lastname, ', ', d5.firstname) AS d5name", 'd5.firstname AS d5firstname',
            'd5.alternatename AS d5alternatename', "CONCAT(d6.lastname, ', ', d6.firstname) AS d6name",
            'd6.firstname AS d6firstname', 'd6.alternatename AS d6alternatename', 'r.has_lived_in_double AS liveddouble',
            "'' AS roommate", "CONCAT(ru.lastname, ', ', ru.firstname) AS rname", 'ru.firstname AS rfirstname',
            'ru.alternatename AS ralternatename'
        );
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_dorm} d ON s.dormid = d.id',
            '{local_mxschool_rooming} r ON s.userid = r.userid', '{user} d1 ON r.dormmate1id = d1.id',
            '{user} d2 ON r.dormmate2id = d2.id', '{user} d3 ON r.dormmate3id = d3.id', '{user} d4 ON r.dormmate4id = d4.id',
            '{user} d5 ON r.dormmate5id = d5.id', '{user} d6 ON r.dormmate6id = d6.id',
            '{user} ru ON r.preferred_roommateid = ru.id'
        );
        $where = array(
            'u.deleted = 0', "s.boarding_status_next_year = 'Boarder'", $filter->submitted === '1'
            ? "EXISTS (SELECT userid FROM {local_mxschool_rooming} WHERE userid = u.id)" : (
                $filter->submitted === '0'
                ? "NOT EXISTS (SELECT userid FROM {local_mxschool_rooming} WHERE userid = u.id)" : ''
            ), $filter->gender ? "s.gender = '{$filter->gender}'" : '',
            $filter->roomtype ? "r.room_type = '{$filter->roomtype}'" : '',
            $filter->double !== '' ? "r.has_lived_in_double = {$filter->double}" : ''
        );
        $sortable = array('student', 'grade', 'dorm', 'roommate');
        $urlparams = array(
            'submitted' => $filter->submitted, 'gender' => $filter->gender, 'roomtype' => $filter->roomtype,
            'double' => $filter->double, 'search' => $filter->search
        );
        $centered = array('grade', 'gender', 'roomtype', 'liveddouble');
        $searchable = array(
            'u.firstname', 'u.lastname', 'u.alternatename', 'd1.firstname', 'd1.lastname', 'd1.alternatename', 'd2.firstname',
            'd2.lastname', 'd2.alternatename', 'd3.firstname', 'd3.lastname', 'd3.alternatename', 'd4.firstname', 'd4.lastname',
            'd4.alternatename', 'd5.firstname', 'd5.lastname', 'd5.alternatename', 'd6.firstname', 'd6.lastname',
            'd6.alternatename', 'ru.firstname', 'ru.lastname', 'ru.alternatename'
        );
        parent::__construct(
            'rooming_table', $columns, $headers, $sortable, 'student', $fields, $from, $where, $urlparams, $centered,
            $filter->search, $searchable
        );
    }

    /**
     * Formats the dormmates column to a list of "last, first (alternate)" or "last, first".
     */
    protected function col_dormmates($values) {
        $dormmates = array();
        for ($i = 1; $i <= 6; $i++) {
            $dormmates[] = $values->{"d{$i}name"}.(
                $values->{"d{$i}alternatename"} && $values->{"d{$i}alternatename"} !== $values->{"d{$i}firstname"}
                ? " ({$values->{"d{$i}alternatename"}})" : ''
            );
        }
        return implode('<br>', $dormmates);
    }

    /**
     * Formats the lived double column to "Yes" / "No".
     */
    protected function col_liveddouble($values) {
        return isset($values->liveddouble) ? ($values->liveddouble ? get_string('yes') : get_string('no')) : '';
    }

    /**
     * Formats the roommate column to "last, first (alternate)" or "last, first".
     */
    protected function col_roommate($values) {
        return $values->rname.(
            $values->ralternatename && $values->ralternatename !== $values->rfirstname ? " ({$values->ralternatename})" : ''
        );
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return isset($values->rid) ? $this->edit_icon('/local/mxschool/rooming/rooming_enter.php', $values->rid) : '';
    }

}
