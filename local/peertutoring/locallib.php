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
 * Local functions for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Queries the database to create a list of all the peer tutoring departments.
 *
 * @return array The departments as id => name, ordered alphabetically by department name.
 */
function get_department_list() {
    global $DB;
    $list = array();
    $departments = $DB->get_records_sql("SELECT id, name FROM {local_peertutoring_dept} WHERE deleted = 0 ORDER BY name");
    if ($departments) {
        foreach ($departments as $department) {
            $list[$department->id] = $department->name;
        }
    }
    return $list;
}
