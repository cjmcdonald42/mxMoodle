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
        $fields; $from; $where = array('u.id > 0', 'u.deleted = 0');
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
                $fields = array(
                    STUDENT_NAME,
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
                $where = array_merge($where, array(
                    $filter->dorm ? "d.name = $filter->dorm" : '',
                    $filter->search ? "(u.firstname LIKE '%$search%'
                                        OR u.lastname LIKE '%$search%'
                                        OR u.alternatename LIKE '%$search%'
                                       )" : ''
                                       // TODO: add advisor.
                ));
                break;
            case 'permissions':

                break;
            case 'parents':

                break;
        }

        parent::__construct($uniqueid, $columns, $headers);
        $this->set_sql(implode(', ', $fields), $from, implode(' AND ', array_filter($where)));
    }

}
