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
 * Generic checkin sheet table for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage checkin
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class generic_table extends local_mxschool_table {

    /**
     * Creates a new generic_table.
     *
     * @param stdClass $filter Any filtering for the table - could include property dorm.
     */
    public function __construct($filter) {
        global $DB;
        $columns = array('student', 'dorm', 'room', 'grade', 'checkin');
        if ($filter->dorm) {
            unset($columns[array_search('dorm', $columns)]);
            if ($DB->get_field('local_mxschool_dorm', 'type', array('id' => $filter->dorm)) === 'Day') {
                unset($columns[array_search('room', $columns)]);
            }
        }
        $headers = array_map(function($column) {
            return get_string("checkin_generic_report_header_{$column}", 'local_mxschool');
        }, $columns);
        $fields = array(
            's.id', 's.userid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'd.name AS dorm', 's.room', 's.grade',
            "'' AS checkin"
        );
        $from = array('{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_dorm} d ON s.dormid = d.id');
        $where = array('u.deleted = 0', $filter->dorm ? "s.dormid = {$filter->dorm}" : '');
        $sortable = array('student', 'dorm', 'room', 'grade');
        if (!$filter->dorm) {
            unset($sortable[array_search('room', $sortable)]);
        }
        $urlparams = array('dorm' => $filter->dorm);
        $centered = array('room', 'grade');
        parent::__construct(
            'checkin_table', $columns, $headers, $sortable, 'student', $fields, $from, $where, $urlparams, $centered
        );
    }

    /**
     * Formats the student column to "last, first (preferred)" or "last, first".
     */
    protected function col_student($values) {
        return format_student_name($values->userid);
    }

}
