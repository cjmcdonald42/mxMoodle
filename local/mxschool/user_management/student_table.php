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
        $columns = array('student');
        $fields = array("CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname', 'u.alternatename');
        $from = array('{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_dorm} d ON s.dormid = d.id');
        $where = array('u.deleted = 0', $filter->dorm ? "d.id = $filter->dorm" : '');
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename');
        switch($type) {
            case 'students':
                $columns = array_merge($columns, array(
                    'grade',
                    'advisor',
                    'dorm',
                    'room',
                    'phone',
                    'birthday'
                ));
                $fields = array_merge(array('s.id'), $fields, array(
                    's.grade',
                    "CONCAT(a.lastname, ', ', a.firstname) AS advisor",
                    'd.name AS dorm',
                    's.room',
                    's.phone_number AS phone',
                    's.birthdate AS birthday'
                ));
                $from[] = '{user} a ON s.advisorid = a.id';
                $searchable = array_merge($searchable, array('a.firstname', 'a.lastname'));
                break;

            case 'permissions':
                $columns = array_merge($columns, array(
                    'overnight',
                    'riding',
                    'comment',
                    'rideshare',
                    'boston',
                    'town',
                    'passengers',
                    'swimcompetent',
                    'swimallowed',
                    'boatallowed'
                ));
                $fields = array_merge(array('p.id'), $fields, array(
                    'p.overnight',
                    'p.may_ride_with AS riding',
                    'p.ride_permission_details AS comment',
                    'p.ride_share AS rideshare',
                    'p.may_drive_to_boston AS boston',
                    'p.may_drive_to_town AS town',
                    'p.may_drive_passengers AS passengers',
                    'p.swim_competent AS swimcompetent',
                    'p.swim_allowed AS swimallowed',
                    'p.boat_allowed AS boatallowed'
                ));
                $from[] = '{local_mxschool_permissions} p ON u.id = p.userid';
                break;

            case 'parents':
                $columns = array_merge($columns, array(
                    'parent',
                    'primaryparent',
                    'relationship',
                    'homephone',
                    'cellphone',
                    'workphone',
                    'email'
                ));
                $fields = array_merge(array('p.id'), $fields, array(
                    'p.parent_name AS parent',
                    'p.is_primary_parent AS primaryparent',
                    'p.relationship',
                    'p.home_phone AS homephone',
                    'p.cell_phone AS cellphone',
                    'p.work_phone AS workphone',
                    'p.email'
                ));
                $from[] = '{local_mxschool_parent} p ON u.id = p.userid';
                $searchable[] = 'p.parent_name';
                break;
        }
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("student_report_{$type}_header_{$column}", 'local_mxschool');
        }
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');

        $sortable = array('student', 'grade', 'advisor', 'dorm', 'room', 'birthday', 'parent');
        $urlparams = array('type' => $type, 'dorm' => $filter->dorm, 'search' => $filter->search);
        parent::__construct($uniqueid, $columns, $headers, $sortable, 'student', $fields, $from, $where, $searchable, $filter->search, $urlparams);
    }

    /**
     * Formats the student column to "last, first (alternate)" or "last, first".
     */
    protected function col_student($values) {
        $alternatename = $values->alternatename && $values->alternatename !== $values->firstname ? " ($values->alternatename)" : '';
        return $values->student . $alternatename;
    }

    /**
     * Formats the birthday column to "mm/dd".
     */
    protected function col_birthday($values) {
        $date = new DateTime($values->birthday, core_date::get_server_timezone_object());
        return $date->format('m/d');
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/mxschool/user_management/student_edit.php', $values->id);
    }

}
