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
 * Course table for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../mxschool/classes/mx_table.php');

class course_table extends local_mxschool_table {

    /**
     * Creates a new course_table.
     *
     * @param string $uniqueid a unique identifier for the table.
     */
    public function __construct($uniqueid) {
        $columns = array('name', 'department');
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("course_report_header_{$column}", 'local_peertutoring');
        }
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $fields = array('c.id', 'c.name', 'd.name AS department');
        $from = array('{local_peertutoring_course} c', '{local_peertutoring_dept} d ON c.departmentid = d.id');
        $where = array('c.deleted = 0', 'd.deleted = 0');
        $sortable = array('name', 'department');
        $urlparams = array();
        $centered = array('name', 'department');
        parent::__construct($uniqueid, $columns, $headers, $sortable, 'name', $fields, $from, $where, $urlparams, $centered);
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/peertutoring/course_edit.php', $values->id).$this->delete_icon($values->id, 'course');
    }

}
