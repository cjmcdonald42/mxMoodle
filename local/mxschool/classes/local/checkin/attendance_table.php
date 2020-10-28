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
 * Interactive check-in sheet table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  checkin
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\checkin;

defined('MOODLE_INTERNAL') || die();

 use local_mxschool\output\checkbox;

class attendance_table extends \local_mxschool\table {

    /**
     * Creates a new attendance_table.
     *
     * @param stdClass $filter Any filtering for the table - could include property dorm.
     */
    public function __construct($filter, $download) {
        $this->is_downloading($download, 'Attendance Report', 'Attendance Report');
        global $DB;
        $columns = array('student', 'dorm', 'room', 'grade', 'attended');
        if ($filter->dorm > 0) {
            unset($columns[array_search('dorm', $columns)]);
            if ($DB->get_field('local_mxschool_dorm', 'type', array('id' => $filter->dorm)) === 'Day') {
                unset($columns[array_search('room', $columns)]); // Day houses don't have rooms.
            }
        }
        if ($filter->dorm == -1) {
            unset($columns[array_search('room', $columns)]);
        }
        $headers = $this->generate_headers($columns, 'checkin:attendance_report');
        $sortable = array('student', 'room', 'grade');
        $centered = array('student', 'room', 'grade', 'attended');
        if ($filter->dorm <= 0) {
            unset($sortable[array_search('room', $sortable)]);
        }
        parent::__construct('attendance_table', $columns, $headers, $sortable, $centered, $filter, false);

        $fields = array('s.id', 's.userid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 's.dormid', 's.room', 's.grade', 'at.id AS atid', 'at.attended');
        $from = array('{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_dorm} d ON s.dormid = d.id', '{local_mxschool_attendance} at ON s.userid = at.userid');
        $where = array('u.deleted = 0');
        if ($filter->dorm) {
            $where[] = $this->get_dorm_where($filter->dorm);
        }
	   if ($filter->attendance) {
		   $attended = $filter->attendance==='Attended' ? 1 : 0;
		   $where[] = "at.attended = {$attended}";
	   }
        $this->define_sql($fields, $from, $where);
    }

    /**
     * Formats the attended cloumn to a javascript checkbox
     */
    protected function col_attended($values) {
	    if($this->is_downloading()) return $values->attended ? 'X' : '';
	    global $PAGE;
	    $output = $PAGE->get_renderer('local_mxschool');
	    $renderable = new checkbox($values->atid, 'local_mxschool_attendance', 'attended', $values->attended);
	    return $output->render($renderable);
    }

}
