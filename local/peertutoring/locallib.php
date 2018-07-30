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
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../mxschool/locallib.php');

/**
 * Determines whether a specified user is a student who is permitted to access the tutoring form.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access the tutoring form.
 */
function student_may_access_tutoring($userid) {
    return array_key_exists($userid, get_tutor_list());
}

/**
 * Queries the database to create a list of all the peer tutoring departments.
 *
 * @return array The departments as id => name, ordered alphabetically by department name.
 */
function get_department_list() {
    global $DB;
    $departments = $DB->get_records_sql("SELECT id, name FROM {local_peertutoring_dept} WHERE deleted = 0 ORDER BY name");
    return convert_records_to_list($departments);
}

/**
 * Queries the database to create a list of all the peer tutoring departments for which a tutor is approved to tutor.
 *
 * @param int $userid The user id of the tutor whose approved departments will be checked.
 * @return array The departments as id => name, ordered alphabetically by department name.
 */
function get_tutor_department_list($userid) {
    global $DB;
    $list = array(0 => get_string('form_select_default', 'local_mxschool'));
    $json = $DB->get_field('local_peertutoring_tutor', 'departments', array('userid' => $userid));
    if ($json) {
        $approved = json_decode($json);
        if (count($approved)) {
            $wherestring = implode(' OR ', array_map(function($departmentid) {
                return "id = $departmentid";
            }, $approved));
            $departments = $DB->get_records_sql(
                "SELECT id, name FROM {local_peertutoring_dept} WHERE deleted = 0 AND $wherestring ORDER BY name"
            );
            $list += convert_records_to_list($departments);
        }
    }
    return $list;
}

/**
 * Queries the database to create a list of all the peer tutoring courses.
 *
 * @return array The courses as id => name, ordered alphabetically by course name.
 */
function get_course_list() {
    global $DB;
    $courses = $DB->get_records_sql("SELECT id, name FROM {local_peertutoring_course} WHERE deleted = 0 ORDER BY name");
    return convert_records_to_list($courses);
}

/**
 * Queries the database to create a list of all the peer tutoring courses within a specified department.
 *
 * @param int $departmentid The id of the department to look for.
 * @return array The courses as id => name, ordered alphabetically by course name.
 */
function get_department_course_list($departmentid) {
    global $DB;
    $courses = $DB->get_records_sql(
        "SELECT id, name FROM {local_peertutoring_course} WHERE deleted = 0 AND departmentid = ? ORDER BY name",
        array($departmentid)
    );
    return array(0 => get_string('form_select_default', 'local_mxschool')) + convert_records_to_list($courses);
}

/**
 * Queries the database to create a list of all the peer tutoring types.
 *
 * @return array The types as id => displaytext, unordered.
 */
function get_type_list() {
    global $DB;
    $types = $DB->get_records_sql("SELECT id, displaytext AS name FROM {local_peertutoring_type} WHERE deleted = 0");
    return convert_records_to_list($types);
}

/**
 * Queries the database to create a list of all the peer tutoring effectiveness ratings.
 *
 * @return array The ratings as id => display text, ordered alphabetically by display text.
 */
function get_rating_list() {
    global $DB;
    $ratings = $DB->get_records_sql(
        "SELECT id, displaytext AS name FROM {local_peertutoring_rating} WHERE deleted = 0 ORDER BY displaytext"
    );
    return convert_records_to_list($ratings);
}

/**
 * Queries the database to create a list of all the peer tutors.
 *
 * @return array The tutors as userid => name, ordered alphabetically by tutor name.
 */
function get_tutor_list() {
    global $DB;
    $tutors = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_peertutoring_tutor} t LEFT JOIN {user} u ON t.userid = u.id WHERE u.deleted = 0 ORDER BY name"
    );
    return convert_records_to_list($tutors);
}

/**
 * Queries the database to create a list of all the dates for which there are tutoring records.
 *
 * @return array The dates for which there are tutoring records as timestamp => date (mm/dd/yy), in descending order by date.
 */
function get_tutoring_date_list() {
    global $DB;
    $list = array();
    $records = $DB->get_records_sql(
        "SELECT s.id, s.tutoring_date
         FROM {local_peertutoring_session} s LEFT JOIN {user} t ON s.tutorid = t.id LEFT JOIN {user} u ON s.studentid = u.id
         WHERE s.deleted = 0 AND t.deleted = 0 AND u.deleted = 0 ORDER BY tutoring_date DESC"
    );
    if ($records) {
        foreach ($records as $record) {
            $date = new DateTime('now', core_date::get_server_timezone_object());
            $date->setTimestamp($record->tutoring_date);
            $date->modify('midnight');
            if (!array_key_exists($date->getTimestamp(), $list)) {
                $list[$date->getTimestamp()] = $date->format('m/d/y');
            }
        }
    }
    return $list;
}
