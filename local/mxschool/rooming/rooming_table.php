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
 * Rooming table for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage rooming
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class rooming_table extends local_mxschool_table {

    /**
     * Creates a new rooming_table.
     *
     * @param stdClass $filter Any filtering for the table - could include submitted, gender, roomtype, double, and search.
     * @param string $download Indicates whether the table is downloading.
     */
    public function __construct($filter, $download) {
        $this->is_downloading($download, 'Rooming Requests', 'Rooming Requests');
        $columns = array('student', 'grade', 'gender', 'dorm', 'liveddouble', 'roomtype', 'dormmates', 'roommate');
        if ($filter->gender !== '') {
            unset($columns[array_search('gender', $columns)]);
        }
        if ($filter->roomtype !== '') {
            unset($columns[array_search('roomtype', $columns)]);
        }
        if ($filter->double !== '') {
            unset($columns[array_search('liveddouble', $columns)]);
        }
        $headers = array_map(function($column) {
            return get_string("rooming_report_header_{$column}", 'local_mxschool');
        }, $columns);
        if (!$this->is_downloading()) {
            $columns[] = 'actions';
            $headers[] = get_string('report_header_actions', 'local_mxschool');
        }
        $fields = array(
            's.id', 'u.id AS userid', 'r.id AS rid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname',
            'u.alternatename', 's.grade', 's.gender', 'd.name AS dorm', 'r.has_lived_in_double AS liveddouble',
            'r.room_type AS roomtype', "'' AS dormmates", 'ru.id as ruid', 'ru.deleted as rdeleted'
        );
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_dorm} d ON s.dormid = d.id',
            '{local_mxschool_rooming} r ON s.userid = r.userid', '{user} ru ON r.preferred_roommateid = ru.id'
        );
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'ru.firstname', 'ru.lastname', 'ru.alternatename');
        for ($i = 1; $i <= 6; $i++) {
            $fields = array_merge($fields, array(
                "d{$i}u.id AS d{$i}id", "d{$i}s.grade AS d{$i}grade"
            ));
            $from = array_merge($from, array(
                "{user} d{$i}u ON r.dormmate{$i}id = d{$i}u.id",
                "{local_mxschool_student} d{$i}s ON r.dormmate{$i}id = d{$i}s.userid"
            ));
            $searchable = array_merge($searchable, array("d{$i}u.firstname", "d{$i}u.lastname", "d{$i}u.alternatename"));
        }
        $where = array(
            'u.deleted = 0', 's.grade <> 12', "s.boarding_status_next_year = 'Boarder'",
            $filter->gender ? "s.gender = '{$filter->gender}'" : '',
            $filter->roomtype ? "r.room_type = '{$filter->roomtype}'" : '',
            $filter->double !== '' ? "r.has_lived_in_double = {$filter->double}" : ''
        );
        switch ($filter->submitted) {
            case '1':
                $where[] = "EXISTS (SELECT userid FROM {local_mxschool_rooming} WHERE userid = u.id)";
                break;
            case '0':
                $where[] = "NOT EXISTS (SELECT userid FROM {local_mxschool_rooming} WHERE userid = u.id)";
                break;
        }
        $sortable = array('student', 'grade', 'dorm');
        $urlparams = array(
            'submitted' => $filter->submitted, 'gender' => $filter->gender, 'roomtype' => $filter->roomtype,
            'double' => $filter->double, 'search' => $filter->search
        );
        $centered = array('grade', 'gender', 'liveddouble', 'roomtype');
        parent::__construct(
            'rooming_table', $columns, $headers, $sortable, 'student', $fields, $from, $where, $urlparams, $centered,
            $filter->search, $searchable
        );
    }

    /**
     * Formats the lived double column to "Yes" / "No".
     */
    protected function col_liveddouble($values) {
        return isset($values->rid) ? format_boolean($values->liveddouble) : '';
    }

    /**
     * Formats the dormmates column to a list of "last, first (alternate)" or "last, first".
     */
    protected function col_dormmates($values) {
        if (!isset($values->rid)) {
            return '';
        }
        $dormmates = array();
        for ($i = 1; $i <= 6; $i++) {
            $dormmates[] = format_student_name_userid($values->{"d{$i}id"}) . " ({$values->{"d{$i}grade"}})";
        }
        return implode($this->is_downloading() ? "\n" : '<br>', $dormmates);
    }

    /**
     * Formats the roommate column to "last, first (alternate)" or "last, first".
     */
    protected function col_roommate($values) {
        return isset($values->rid) ? format_student_name_userid($values->ruid) : '';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return isset($values->rid) ? $this->edit_icon('/local/mxschool/rooming/rooming_enter.php', $values->rid) : '';
    }

}
