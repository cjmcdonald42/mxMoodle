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
 * @subpackage user_management
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class student_table extends local_mxschool_table {

    /** @var string $type the type of report - either 'students', 'permissions', or 'parents'.*/
    private $type;

    /**
     * Creates a new student_table.
     *
     * @param string $type The type of report - either 'students', 'permissions', or 'parents'.
     * @param stdClass $filter Any filtering for the table - could include dorm or search.
     */
    public function __construct($type, $filter) {
        global $DB;
        $this->type = $type;
        $columns = array('student');
        $fields = array("CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname', 'u.alternatename');
        $from = array('{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_dorm} d ON s.dormid = d.id');
        $where = array('u.deleted = 0', $filter->dorm ? "d.id = {$filter->dorm}" : '');
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename');
        switch($type) {
            case 'students':
                $columns = array_merge($columns, array('grade', 'advisor', 'dorm', 'room', 'phone', 'birthday'));
                if ($filter->dorm) {
                    unset($columns[array_search('dorm', $columns)]);
                    if ($DB->get_field('local_mxschool_dorm', 'type', array('id' => $filter->dorm)) === 'Day') {
                        unset($columns[array_search('room', $columns)]);
                    }
                }
                $fields = array_merge(array('s.id'), $fields, array(
                    's.grade', "CONCAT(a.lastname, ', ', a.firstname) AS advisor", 'd.name AS dorm', 's.room',
                    's.phone_number AS phone', 's.birthday')
                );
                $from[] = '{user} a ON s.advisorid = a.id';
                $searchable = array_merge($searchable, array('a.firstname', 'a.lastname'));
                break;
            case 'permissions':
                $columns = array_merge($columns, array(
                    'overnight', 'license', 'driving', 'passengers', 'riding', 'ridingcomment', 'rideshare', 'boston',
                    'swimcompetent', 'swimallowed', 'boatallowed'
                ));
                $fields = array_merge(array('p.id', 's.id AS sid'), $fields, array(
                    'p.overnight', 'p.license_date AS license', 'p.may_drive_to_town AS driving',
                    'p.may_drive_passengers AS passengers', 'p.may_ride_with AS riding',
                    'p.ride_permission_details AS ridingcomment', 'p.ride_share AS rideshare',
                    'p.may_drive_to_boston AS boston', 'p.swim_competent AS swimcompetent', 'p.swim_allowed AS swimallowed',
                    'p.boat_allowed AS boatallowed'
                ));
                $from[] = '{local_mxschool_permissions} p ON u.id = p.userid';
                break;
            case 'parents':
                $columns = array_merge($columns, array(
                    'parent', 'primaryparent', 'relationship', 'homephone', 'cellphone', 'workphone', 'email'
                ));
                $fields = array_merge(array('p.id'), $fields, array(
                    'p.parent_name AS parent', 'p.is_primary_parent AS primaryparent', 'p.relationship',
                    'p.home_phone AS homephone', 'p.cell_phone AS cellphone', 'p.work_phone AS workphone', 'p.email'
                ));
                $from[] = '{local_mxschool_parent} p ON u.id = p.userid';
                $where = array_merge($where, array('p.deleted = 0'));
                $searchable[] = 'p.parent_name';
                break;
        }
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("user_management_student_report_{$type}_header_{$column}", 'local_mxschool');
        }
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');

        $sortable = array('student', 'grade', 'advisor', 'dorm', 'room', 'birthday', 'parent');
        if (!$filter->dorm) {
            unset($sortable[array_search('room', $sortable)]);
        }
        $urlparams = array('type' => $type, 'dorm' => $filter->dorm, 'search' => $filter->search);
        $centered = array(
            'grade', 'room', 'birthday', 'overnight', 'license', 'driving', 'passengers', 'rideshare', 'boston', 'swimcompetent',
            'swimallowed', 'boatallowed', 'primaryparent'
        );
        parent::__construct(
            'student_table', $columns, $headers, $sortable, 'student', $fields, $from, $where, $urlparams, $centered,
            $filter->search, $searchable
        );
    }

    /**
     * Formats the license column to 'n/j/y'.
     */
    protected function col_license($values) {
        return $values->license ? date('n/j/y', $values->license) : '';
    }

    /**
     * Formats the birthday column to 'n/j/y'.
     */
    protected function col_birthday($values) {
        $birthday = new DateTime($values->birthday, core_date::get_server_timezone_object());
        return $birthday->format('n/j/y');
    }

    /**
     * Formats the primary parent column to a check or nothing.
     */
    protected function col_primaryparent($values) {
        return $values->primaryparent ? '&#10003;' : '';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return (
            $this->type === 'students' ? $this->edit_icon('/local/mxschool/user_management/student_edit.php', $values->id) : (
                $this->type === 'permissions' ? $this->edit_icon('/local/mxschool/user_management/student_edit.php', $values->sid)
                : $this->edit_icon('/local/mxschool/user_management/parent_edit.php', $values->id).$this->delete_icon($values->id)
            )
        );
    }

}
