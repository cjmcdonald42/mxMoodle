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
 * Course table for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class course_table extends local_mxschool\table {

    /**
     * Creates a new course_table.
     */
    public function __construct() {
        $columns = array('name', 'department');
        $headers = $this->generate_headers($columns, 'course_report', 'local_peertutoring');
        $sortable = array('name', 'department');
        $centered = array('name', 'department');
        parent::__construct('course_table', $columns, $headers, $sortable, $centered);

        $fields = array('c.id', 'c.name', 'd.name AS department');
        $from = array('{local_peertutoring_course} c', '{local_peertutoring_dept} d ON c.departmentid = d.id');
        $where = array('c.deleted = 0', 'd.deleted = 0');
        $this->set_sql($fields, $from, $where);
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/peertutoring/course_edit.php', $values->id) . $this->delete_icon($values->id, 'course');
    }

}
