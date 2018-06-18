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
 * Weekend calculator table for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class weekend_calculator_table extends local_mxschool_table {

    /** @var int The semester being displayed on the table. */
    private $semester;

    /**
     * Creates a new weekend_calculator_table.
     *
     * @param string $uniqueid A unique identifier for the table.
     * @param stdClass $filter Any filtering for the table - could include a dorm or semester filter.
     * @param array $weekends The records of the weekends to include in the table.
     * @param bool $isstudent Whether the user is a student and only their record should be displayed.
     */
    public function __construct($uniqueid, $filter, $weekends, $isstudent) {
        global $USER;
        $this->semester = $filter->semester;
        $columns1 = array('student', 'grade');
        $headers1 = array();
        foreach ($columns1 as $column) {
            $headers1[] = get_string("weekend_calculator_report_header_{$column}", 'local_mxschool');
        }
        $columns2 = array('total', 'allowed');
        $headers2 = array();
        foreach ($columns2 as $column) {
            $headers2[] = get_string("weekend_calculator_report_header_{$column}", 'local_mxschool');
        }
        $fields = array(
            's.id', 's.userid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname', 'u.alternatename', 's.grade',
            "'' AS total", "'' AS allowed"
        );
        $centered = array('grade', 'total', 'allowed');
        $offcampus = get_string('weekend_report_abbreviation_offcampus', 'local_mxschool');
        $free = get_string('weekend_report_abbreviation_free', 'local_mxschool');
        $closed = get_string('weekend_report_abbreviation_closed', 'local_mxschool');
        foreach ($weekends as $weekend) {
            $columns1[] = $centered[] = "weekend_$weekend->id";
            $date = new DateTime('now', core_date::get_server_timezone_object());
            $date->setTimestamp($weekend->sunday_time);
            $date->modify("-1 day");
            $headers1[] = $date->format('m/d');
            $fields[] = "CASE
                WHEN (SELECT type FROM {local_mxschool_weekend} WHERE id = $weekend->id) = 'free' THEN '$free'
                WHEN (SELECT COUNT(id) FROM {local_mxschool_weekend_form} WHERE weekendid = $weekend->id AND userid = s.userid
                                                                                AND active = 1) = 1 THEN '$offcampus'
                WHEN (SELECT type FROM {local_mxschool_weekend} WHERE id = $weekend->id) = 'closed' THEN '$closed'
                ELSE ''
            END AS weekend_$weekend->id";
        }
        $columns = array_merge($columns1, $columns2);
        $headers = array_merge($headers1, $headers2);
        $from = array(
            '{local_mxschool_student} s', '{user} u ON s.userid = u.id'
        );
        $where = array('u.deleted = 0');
        $where[] = $isstudent ? "s.userid = $USER->id" : ($filter->dorm ? "s.dormid = $filter->dorm" : '');
        $sortable = array('student', 'grade');
        $urlparams = array('dorm' => $filter->dorm, 'semester' => $filter->semester);
        parent::__construct($uniqueid, $columns, $headers, $sortable, 'student', $fields, $from, $where, $urlparams, $centered);
    }

    /**
     * Formats the total column to indicate the number of weekends each student has used.
     */
    protected function col_total($values) {
        return calculate_weekends_used($values->userid, $this->semester);
    }

    /**
     * Formats the allowed column to indicate the number of weekends each student is allowed.
     */
    protected function col_allowed($values) {
        return calculate_weekends_allowed($values->userid, $this->semester)
        ?: get_string('weekend_report_abbreviation_unlimited', 'local_mxschool');
    }

}
