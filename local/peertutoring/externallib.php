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
 * External Functions for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once('locallib.php');

class local_peertutoring_external extends external_api {

    /**
     * Returns descriptions of the get_department_courses() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_department_courses() function.
     */
    public static function get_department_courses_parameters() {
        return new external_function_parameters(array(
            'department' => new external_value(PARAM_INT, 'The id of the department to query for.')
        ));
    }

    /**
     * Queries the database to find all courses in a specified department.
     *
     * @param int $department The id of the department to query for.
     * @return array The courses in the department as {id, name}.
     */
    public static function get_department_courses($department) {
        external_api::validate_context(context_system::instance());
        require_capability('local/peertutoring:enter_tutoring', context_system::instance());
        $params = self::validate_parameters(self::get_department_courses_parameters(), array('department' => $department));

        $list = get_department_course_list($params['department']);
        $result = array();
        foreach ($list as $courseid => $name) {
            $result[] = array('id' => $courseid, 'name' => $name);
        }
        return $result;
    }

    /**
     * Returns a description of the get_department_courses() function's return values.
     *
     * @return external_multiple_structure Object describing the return values of the get_department_courses() function.
     */
    public static function get_department_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(array(
                'id' => new external_value(PARAM_INT, 'id of the course'),
                'name' => new external_value(PARAM_TEXT, 'name of the course')
            ))
        );
    }

    /**
     * Returns descriptions of the get_tutor_options() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_tutor_options() function.
     */
    public static function get_tutor_options_parameters() {
        return new external_function_parameters(array('userid' => new external_value(PARAM_INT, 'The user id of the tutor.')));
    }

    /**
     * Queries the database to determine the approved departments for a specified tutor.
     *
     * @param int $userid The user id of the student.
     * @return array The approved departments as {id, name}.
     */
    public static function get_tutor_options($userid) {
        external_api::validate_context(context_system::instance());
        require_capability('local/peertutoring:enter_tutoring', context_system::instance());
        $params = self::validate_parameters(self::get_tutor_options_parameters(), array('userid' => $userid));

        global $DB;
        $list = get_tutor_department_list($params['userid']);
        $departments = array();
        foreach ($list as $id => $name) {
            $departments[] = array('id' => $id, 'name' => $name);
        }
        return $departments;
    }

    /**
     * Returns a description of the get_tutor_options() function's return values.
     *
     * @return external_multiple_structure Object describing the return values of the get_tutor_options() function.
     */
    public static function get_tutor_options_returns() {
        return new external_multiple_structure(new external_single_structure(array(
            'id' => new external_value(PARAM_INT, 'id of the department'),
            'name' => new external_value(PARAM_TEXT, 'name of the department')
        )));
    }

}
