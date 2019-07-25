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
 * Student vehicle registration table for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  user_management
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\user_management;

defined('MOODLE_INTERNAL') || die();

class vehicle_table extends \local_mxschool\table {

    /**
     * Creates a new vehicle_table.
     *
     * @param stdClass $filter Any filtering for the table - could include property search.
     */
    public function __construct($filter) {
        $columns = array('student', 'grade', 'phone', 'license', 'make', 'model', 'color', 'registration');
        $headers = $this->generate_headers($columns, 'user_management_vehicle_report');
        $sortable = array('student', 'grade', 'license', 'make', 'model', 'color');
        $centered = array('grade', 'license');
        parent::__construct('vehicle_table', $columns, $headers, $sortable, $centered, $filter);

        $fields = array(
            'v.id', 's.userid', "CONCAT(u.lastname, ', ', u.firstname) AS student", 's.grade', 's.phone_number AS phone',
            'p.license_date AS license', 'v.make', 'v.model', 'v.color', 'v.registration'
        );
        $from = array(
            '{local_mxschool_vehicle} v', '{user} u ON v.userid = u.id', '{local_mxschool_student} s ON v.userid = s.userid',
            '{local_mxschool_permissions} p ON v.userid = p.userid'
        );
        $where = array('v.deleted = 0', 'u.deleted = 0');
        $searchable = array('u.firstname', 'u.lastname', 'u.alternatename', 'v.make', 'v.model', 'v.color', 'v.registration');
        $this->set_sql($fields, $from, $where);
    }

    /**
     * Formats the license column to 'n/j/y'.
     */
    protected function col_license($values) {
        return $values->license ? format_date('n/j/y', $values->license) : '';
    }

    /**
     * Formats the actions column.
     */
    protected function col_actions($values) {
        return $this->edit_icon('/local/mxschool/user_management/vehicle_edit.php', $values->id) . $this->delete_icon($values->id);
    }

}
