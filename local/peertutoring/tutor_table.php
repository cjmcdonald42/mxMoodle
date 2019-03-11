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
 * Tutor table for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../mxschool/classes/mx_table.php');

class tutor_table extends local_mxschool_table {

    /**
     * Creates a new tutor_table.
     */
    public function __construct() {
        $departments = get_department_list();
        $columns = array('tutor');
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("tutor_report_header_{$column}", 'local_peertutoring');
        }
        $centered = array();
        foreach ($departments as $id => $name) {
            $columns[] = $centered[] = $id;
            $headers[] = $name;
        }
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $fields = array(
            't.id', "CONCAT(u.lastname, ', ', u.firstname) AS tutor", 'u.firstname', 'u.alternatename', 't.departments'
        );
        $from = array('{local_peertutoring_tutor} t', '{user} u ON t.userid = u.id');
        $where = array('u.deleted = 0', 't.deleted = 0');
        $sortable = array('tutor');
        $urlparams = array();
        parent::__construct('tutor_table', $columns, $headers, $sortable, 'tutor', $fields, $from, $where, $urlparams, $centered);
    }

    /**
     * Formats the tutor column to "last, first (alternate)" or "last, first".
     */
    protected function col_tutor($values) {
        return $values->tutor.(
            $values->alternatename && $values->alternatename !== $values->firstname ? " ($values->alternatename)" : ''
        );
    }

    /**
     * Formats the department columns
     */
    public function other_cols($column, $row) {
        if (is_int($column)) {
            return in_array($column, json_decode($row->departments)) ? '&#10003;' : '';
        }
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/peertutoring/tutor_edit.php', $values->id).$this->delete_icon($values->id, 'tutor');
    }

}
