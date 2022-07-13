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
 * Local library functions for Middlesex's Peer Tutoring Subplugin.
 *
 * @package     local_peertutoring
 * @author      mxMoodle Development Team
 * @copyright   2022 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../mxschool/locallib.php');

/*
 * =================================
 * Permissions Validation Functions.
 * =================================
 */

/**
 * Determines whether a specified user is a student who is permitted to access the tutoring form.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access the tutoring form.
 */
function student_may_access_tutoring($userid) {
    return array_key_exists($userid, get_tutor_list());
}

/*
 * ==========================================
 * Database Query for Record List Functions.
 * ==========================================
 */

/**
 * Queries the database to create a list of all the students who are eligible to become peer tutors.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_eligible_student_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND s.grade >= 11
         ORDER BY name"
    );
    return convert_student_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who are eligible to become peer tutors and are not already.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_eligible_unassigned_student_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND s.grade >= 11
            AND NOT EXISTS (SELECT userid FROM {local_peertutoring_tutor} t WHERE userid = u.id and t.deleted = 0)
         ORDER BY name"
    );
    return convert_student_records_to_list($students);
}

/**
 * Queries the database to create a list of all the peer tutoring departments.
 *
 * @return array The departments as id => name, ordered alphabetically by department name.
 */
function get_department_list() {
    global $DB;
    $departments = $DB->get_records_select('local_peertutoring_dept', 'deleted = 0', null, 'value', 'id, name AS value');
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
    $json = $DB->get_field('local_peertutoring_tutor', 'departments', array('userid' => $userid));
    if ($json) {
        $approved = json_decode($json);
        if (count($approved)) {
            $where = implode(' OR ', array_map(function($departmentid) {
                return "id = {$departmentid}";
            }, $approved));
            $departments = $DB->get_records_select(
                'local_peertutoring_dept', "deleted = 0 AND ({$where})", null, 'value', 'id, name AS value'
            );
            return convert_records_to_list($departments);
        }
    }
    return array();
}

/**
 * Queries the database to create a list of all the peer tutoring courses.
 *
 * @return array The courses as id => name, ordered alphabetically by course name.
 */
function get_course_list() {
    global $DB;
    $courses = $DB->get_records_select('local_peertutoring_course', 'deleted = 0', null, 'value', 'id, name AS value');
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
    $courses = $DB->get_records_select(
        'local_peertutoring_course', 'deleted = 0 AND departmentid = ?', array($departmentid), 'value', 'id, name AS value'
    );
    return convert_records_to_list($courses);
}

/**
 * Queries the database to create a list of all the peer tutoring types.
 *
 * @return array The types as id => displaytext, ordered alphabetically by display text.
 */
function get_type_list() {
    global $DB;
    $types = $DB->get_records_select('local_peertutoring_type', 'deleted = 0', null, 'value', 'id, displaytext AS value');
    return convert_records_to_list($types);
}

/**
 * Queries the database to create a list of all the peer tutoring effectiveness ratings.
 *
 * @return array The ratings as id => display text, ordered alphabetically by display text.
 */
function get_rating_list() {
    global $DB;
    $ratings = $DB->get_records_select('local_peertutoring_rating', 'deleted = 0', null, 'value', 'id, displaytext AS value');
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
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_peertutoring_tutor} t LEFT JOIN {user} u ON t.userid = u.id
         WHERE t.deleted = 0 AND u.deleted = 0
         ORDER BY name"
    );
    return convert_student_records_to_list($tutors);
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
        "SELECT s.id, s.tutoring_date AS tutoringdate
         FROM {local_peertutoring_session} s LEFT JOIN {user} tu ON s.tutorid = tu.id LEFT JOIN {user} su ON s.studentid = su.id
                                             LEFT JOIN {local_peertutoring_tutor} t ON s.tutorid = t.userid
                                             LEFT JOIN {local_peertutoring_course} c ON s.courseid = c.id
                                             LEFT JOIN {local_peertutoring_dept} d ON c.departmentid = d.id
                                             LEFT JOIN {local_peertutoring_type} ty ON s.typeid = ty.id
                                             LEFT JOIN {local_peertutoring_rating} r ON s.ratingid = r.id
         WHERE s.deleted = 0 AND tu.deleted = 0 AND su.deleted = 0 AND t.deleted = 0 AND c.deleted = 0 AND d.deleted = 0
                             AND ty.deleted = 0 AND r.deleted = 0
         ORDER BY tutoring_date DESC"
    );
    if ($records) {
        foreach ($records as $record) {
            $date = generate_datetime($record->tutoringdate);
            $date->modify('midnight');
            if (!array_key_exists($date->getTimestamp(), $list)) {
                $list[$date->getTimestamp()] = $date->format('m/d/y');
            }
        }
    }
    return $list;
}

/**
 * Determines the faculty id to display for a faculty user.
 *
 * The priorities of this function are as follows:
 * 1) An id specified as a 'faculty' GET parameter, if the id is valid.
 * 2) The userid of the currently logged in faculty member, if it exists.
 * 3) An empty string.
 *
 * NOTE: The $_GET superglobal is used in this function in order to differentiate between an unset parameter and the all option.
 *       Its value is only used after being checked as numeric or empty to avoid potential security issues.
 *
 * @param bool $includeday Whether to include day houses or limit to boading houses.
 * @return string The user id or an empty string.
 */
function get_param_faculty_advisor() {
    global $DB, $USER;
    if (isset($_GET['advisor']) && (is_numeric($_GET['advisor']) || empty($_GET['advisor']))) {
        $advisor = $_GET['advisor']; // The value is now safe to use.
        // An empty parameter indicates that search has taken place with the all option selected.
        if (empty($advisor) || isset(get_advisor_list()[$advisor])) {
            return $advisor;
        }
    }
    return $DB->get_field('local_mxschool_faculty', 'userid', array('userid' => $USER->id)) ?: '';
}

/*
 * Queries the database to create a list of advisors.
 * @return array advisors as userid => name, ordered alphabetically by advisor name.
 * function convert_student_records_to_list is used to create a list by name.
 */
function get_advisor_list() {
    global $DB;
    $advisors = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_faculty} f LEFT JOIN {user} u ON f.userid = u.id
         WHERE u.deleted = 0
         ORDER BY name"
    );
    return convert_student_records_to_list($advisors);
}
