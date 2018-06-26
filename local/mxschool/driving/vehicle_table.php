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
 * Student vehicle registration table for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../classes/mx_table.php');

class vehicle_table extends local_mxschool_table {

    /**
     * Creates a new vehicle_table.
     *
     * @param string $uniqueid A unique identifier for the table.
     * @param string $search The search for the table.
     */
    public function __construct($uniqueid, $search) {
        $columns = array('student', 'grade', 'phone', 'license', 'make', 'model', 'color', 'registration');
        $headers = array();
        foreach ($columns as $column) {
            $headers[] = get_string("vehicle_report_header_{$column}", 'local_mxschool');
        }
        $columns[] = 'actions';
        $headers[] = get_string('report_header_actions', 'local_mxschool');
        $fields = array(
            'v.id', "CONCAT(u.lastname, ', ', u.firstname) AS student", 'u.firstname', 'u.alternatename', 's.grade',
            's.phone_number AS phone', 'p.license_date AS license', 'v.make', 'v.model', 'v.color', 'v.registration'
        );
        $from = array(
            '{local_mxschool_vehicle} v', '{user} u ON v.userid = u.id', '{local_mxschool_student} s ON v.userid = s.userid',
            '{local_mxschool_permissions} p ON v.userid = p.userid'
        );
        $where = array('v.deleted = 0', 'u.deleted = 0');
        $sortable = array('student', 'grade', 'license', 'make', 'model', 'color');
        $urlparams = array('search' => $search);
        $centered = array('grade', 'license');
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'v.make', 'v.model', 'v.color', 'v.registration');
        parent::__construct(
            $uniqueid, $columns, $headers, $sortable, 'student', $fields, $from, $where, $urlparams, $centered, $search,
            $searchable
        );
    }

    /**
     * Formats the license column to 'n/j/y'.
     */
    protected function col_license($values) {
        return ($values->license ? date('n/j/y', $values->license) : '');
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/mxschool/driving/vehicle_edit.php', $values->id).$this->delete_icon($values->id);
    }

}
