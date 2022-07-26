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
 * Student management table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  user_management
 * @author      mxMoodle Development Team
 * @copyright   2022 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\user_management;

defined('MOODLE_INTERNAL') || die();

class student_table extends \local_mxschool\table {

    /** @var string $type the type of report - either 'students', 'permissions', or 'parents'.*/
    private $type;

    /**
     * Creates a new student_table.
     *
     * @param stdClass $filter Any filtering for the table - could include properties type, dorm, and search.
     */
    public function __construct($filter) {
        global $DB;
        $this->type = $filter->type;
        switch ($filter->type) {
            case 'students':
                $columns = array('student', 'grade', 'advisor', 'dorm', 'room', 'phone', 'birthday', 'intl');
                if ($filter->dorm > 0) {
                    unset($columns[array_search('dorm', $columns)]);
                    if ($DB->get_field('local_mxschool_dorm', 'type', array('id' => $filter->dorm)) === 'Day') {
                        unset($columns[array_search('room', $columns)]); // Day houses don't have rooms.
                    }
                }
                if ($filter->dorm == -1) {
                    unset($columns[array_search('room', $columns)]);
                }
                $sortable = array('student', 'grade', 'advisor', 'dorm', 'room', 'birthday', 'intl');
                $centered = array('grade', 'room', 'birthday', 'intl');
                if ($filter->dorm <= 0) {
                    unset($sortable[array_search('room', $sortable)]);
                }
                break;
            case 'permissions':
                $columns = array(
                    'student', 'overnight', 'may_drive_over_21', 'may_drive_with_anyone', 'travel_to_cities',
				'passengers', 'rideshare', 'swimallowed', 'boatallowed'
                );
                $sortable = array(
				 'student', 'overnight', 'may_drive_over_21', 'may_drive_with_anyone', 'travel_to_cities',
 				'passengers', 'rideshare', 'swimallowed', 'boatallowed'
                );
                $centered = array(
				 'student', 'overnight', 'may_drive_over_21', 'may_drive_with_anyone', 'travel_to_cities',
 				'passengers', 'rideshare', 'swimallowed', 'boatallowed'
                );
                break;
            case 'parents':
                $columns = array(
                    'student', 'parent', 'primaryparent', 'relationship', 'homephone', 'cellphone', 'workphone', 'email'
                );
                $sortable = array('student', 'parent');
                $centered = array('primaryparent');
                break;
        }
        $headers = $this->generate_headers($columns, "user_management:student_report:{$filter->type}");
        parent::__construct('student_table', $columns, $headers, $sortable, $centered, $filter);

        $fields = array('s.userid', "CONCAT(u.lastname, ', ', u.firstname) AS student");
        $from = array('{local_mxschool_student} s', '{user} u ON s.userid = u.id', '{local_mxschool_dorm} d ON s.dormid = d.id');
        $where = array('u.deleted = 0');
        if ($filter->dorm) {
            $where[] = $this->get_dorm_where($filter->dorm);
        }
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename');
        switch ($filter->type) {
            case 'students':
                array_unshift($fields, 's.id');
                array_push(
                    $fields, 's.grade', "CONCAT(a.lastname, ', ', a.firstname) AS advisor", 's.dormid', 'd.name AS dorm', 's.room',
                    's.phone_number AS phone', 's.birthday', 's.intl'
                );
                $from[] = '{user} a ON s.advisorid = a.id';
                array_push($searchable, 'a.firstname', 'a.lastname');
                break;
            case 'permissions':
                array_unshift($fields, 'p.id', 's.id AS sid');
                array_push(
                    $fields,
                    'p.overnight', 'p.may_drive_with_over_21 AS may_drive_over_21', 'p.may_drive_with_anyone',
                    'p.may_travel_to_regional_cities AS travel_to_cities', 'p.may_drive_passengers AS passengers',
				'p.may_use_rideshare AS rideshare', 'p.swim_allowed AS swimallowed', 'p.boat_allowed AS boatallowed'
                );
                $from[] = '{local_mxschool_permissions} p ON u.id = p.userid';
                break;
            case 'parents':
                array_unshift($fields, 'p.id');
                array_push(
                    $fields, 'p.parent_name AS parent', 'p.is_primary_parent AS primaryparent', 'p.relationship',
                    'p.home_phone AS homephone', 'p.cell_phone AS cellphone', 'p.work_phone AS workphone', 'p.email'
                );
                $from[] = '{local_mxschool_parent} p ON u.id = p.userid';
                $where[] = 'p.deleted = 0';
                array_push($searchable, 'p.parent_name', 'p.email');
                break;
        }
        $this->define_sql($fields, $from, $where, $searchable, $filter->search);
    }

    /**
     * Formats the license column to 'n/j/y'.
     */
    protected function col_license($values) {
        return $values->license ? format_date('n/j/y', $values->license) : '';
    }

    /**
     * Formats the birthday column to 'n/j'.
     */
    protected function col_birthday($values) {
        return generate_datetime($values->birthday)->format('n/j');
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
        switch ($this->type) {
            case 'students':
                return $this->edit_icon('/local/mxschool/user_management/student_edit.php', $values->id);
            case 'permissions':
                return $this->edit_icon('/local/mxschool/user_management/student_edit.php', $values->sid);
            case 'parents':
                return $this->edit_icon('/local/mxschool/user_management/parent_edit.php', $values->id)
                       . $this->delete_icon($values->id);
        }
    }

}
