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
 * Student management table for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class student_table extends local_mxschool_table {

    /**
     * Creates a new student_table.
     *
     * @param string $uniqueid a unique identifier for the table.
     * @param string $type the type of report - either 'students', 'permissions', or 'parents'.
     * @param stdClass $filter any filtering for the table - could include dorm or search.
     */
    public function __construct($uniqueid, $type, $filter) {
        $columns = $headers = array();
        switch($type) {
            case 'students':
                $columns = array(
                    'student',
                    'grade',
                    'advisor',
                    'dorm',
                    'room',
                    'phone',
                    'birthday'
                );
                foreach ($columns as $column) {
                    $headers[] = get_string("student_report_header_$column", 'local_mxschool');
                }
                break;
            case 'permissions':

                break;
            case 'parents':

                break;
        }

        parent::__construct($uniqueid, $columns, $headers);

        $fields; $from; $where;
        switch($type) {
            case 'students':
                $this->no_sorting('phone');

                $fields = array(
                    's.id',
                    "CONCAT(u.lastname, ', ', u.firstname) AS student",
                    'u.alternatename',
                    's.grade',
                    "CONCAT(f.lastname, ', ', f.firstname) AS advisor",
                    'd.name AS dorm',
                    's.room',
                    's.phone_number AS phone',
                    's.birthdate AS birthday'
                );
                $from = "{local_mxschool_student} s
               LEFT JOIN {user} u ON s.userid = u.id
               LEFT JOIN {local_mxschool_dorm} d ON s.dormid = d.id
               LEFT JOIN {user} f ON s.advisorid = f.id";
                $where = array(
                    'u.deleted = 0',
                    $filter->dorm ? "d.id = $filter->dorm" : '',
                    $filter->search ? "(
                        u.firstname LIKE '%$filter->search%'
                        OR u.lastname LIKE '%$filter->search%'
                        OR u.alternatename LIKE '%$filter->search%'
                        OR f.firstname LIKE '%$filter->search%'
                        OR f.lastname LIKE '%$filter->search%'
                    )" : ''
                );
                break;
            case 'permissions':

                break;
            case 'parents':

                break;
        }

        $this->set_sql(implode(', ', $fields), $from, implode(' AND ', array_filter($where)));

        $this->define_baseurl(new moodle_url($PAGE->url, array(
            'type' => $type,
            'dorm' => $filter->dorm,
            'search' => $filter->search
        )));
    }

    /**
     * Formats the student column to "last, first (alternate)" or "last, first".
     */
    protected function col_student($values) {
        return $values->student . ($values->alternatename ? " ($values->alternatename)" : '');
    }

    /**
     * Formats the birthday column to "mm/dd".
     */
    protected function col_birthday($values) {
        $date = new DateTime($values->birthday, core_date::get_server_timezone_object());
        return $date->format('m/d');
    }

}
