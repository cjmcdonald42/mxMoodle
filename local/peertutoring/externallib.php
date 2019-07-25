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
 * External Functions for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once(__DIR__.'/locallib.php');

class local_peertutoring_external extends external_api {

    /**
     * Returns descriptions of the get_available_tutors_options() function's parameters.
     *
     * @return external_function_parameters Object holding array of parameters for the get_available_tutors() function.
     */
    public static function get_available_tutors_parameters() {
        return new external_function_parameters(array());
    }

    /**
     * Queries the database to determine the list of students who are available to be added as peer tutors.
     *
     * @return stdClass With property students.
     */
    public static function get_available_tutors() {
        external_api::validate_context(context_system::instance());
        require_capability('local/peertutoring:manage_preferences', context_system::instance());

        $result = new stdClass();
        $result->students = convert_associative_to_object(get_eligible_unassigned_student_list());
        return $result;
    }

    /**
     * Returns a description of the get_available_tutors() function's return values.
     *
     * @return external_single_structure Object describing the return values of the get_available_tutors() function.
     */
    public static function get_available_tutors_returns() {
        return new external_single_structure(array(
            'students' => new external_multiple_structure(new external_single_structure(array(
                'value' => new external_value(PARAM_INT, 'the user id of the student who is eligble to be a new tutor'),
                'text' => new external_value(PARAM_TEXT, 'the name of the student who is eligble to be a new tutor')
            )))
        ));
    }

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
        $params = self::validate_parameters(self::get_department_courses_parameters(), array('department' => $department));

        $list = array(0 => get_string('form_select_default', 'local_mxschool')) + get_department_course_list($params['department']);
        return convert_associative_to_object($list);
    }

    /**
     * Returns a description of the get_department_courses() function's return values.
     *
     * @return external_multiple_structure Object describing the return values of the get_department_courses() function.
     */
    public static function get_department_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure(array(
                'value' => new external_value(PARAM_INT, 'id of the course'),
                'text' => new external_value(PARAM_TEXT, 'name of the course')
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
     * Queries the database to determine the approved departments and possible students to tutor for a specified tutor.
     *
     * @param int $userid The user id of the student.
     * @return stdClass Object with properties departments and students.
     */
    public static function get_tutor_options($userid) {
        external_api::validate_context(context_system::instance());
        $params = self::validate_parameters(self::get_tutor_options_parameters(), array('userid' => $userid));

        $result = new stdClass();
        $list = array(0 => get_string('form_select_default', 'local_mxschool')) + get_tutor_department_list($params['userid']);
        $result->departments = convert_associative_to_object($list);
        $result->students = convert_associative_to_object(get_student_list());
        $result->students = array_filter($result->students, function($student) use($params) {
            return $student['value'] !== $params['userid'];
        });
        return $result;
    }

    /**
     * Returns a description of the get_tutor_options() function's return values.
     *
     * @return external_multiple_structure Object describing the return values of the get_tutor_options() function.
     */
    public static function get_tutor_options_returns() {
        return new external_single_structure(array(
        'departments' => new external_multiple_structure(new external_single_structure(array(
            'value' => new external_value(PARAM_INT, 'id of the department'),
            'text' => new external_value(PARAM_TEXT, 'name of the department')
        ))),
        'students' => new external_multiple_structure(new external_single_structure(array(
            'value' => new external_value(PARAM_INT, 'userid of the student'),
            'text' => new external_value(PARAM_TEXT, 'name of the student')
        )))));
    }

}
