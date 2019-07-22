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
 * Local functions for Middlesex's eSignout Subplugin.
 *
 * @package    local_signout
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../mxschool/locallib.php');
require_once(__DIR__.'/../mxschool/classes/event/record_updated.php');

/**
 * =================================
 * Permissions Validation Functions.
 * =================================
 */

/**
 * Determines whether a specified user is a student who is permitted to access off-campus signout.
 * Students are permitted to participate in off-campus signout if off-campus signout is enabled and the student's grade is 11 or 12.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access off-campus signout.
 */
function student_may_access_off_campus_signout($userid) {
    return get_config('local_signout', 'off_campus_form_enabled')
           && array_key_exists($userid, get_off_campus_permitted_student_list());
}

/**
 * Determines whether a specified user is a student who is permitted to access on-campus signout.
 * Students are permitted to participate in on-campus signout if on-campus signout is enabled and the student is a boarder.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access on-campus signout.
 */
function student_may_access_on_campus_signout($userid) {
    return get_config('local_signout', 'on_campus_form_enabled')
           && array_key_exists($userid, get_on_campus_permitted_student_list());
}

/**
 * Determines whether the current user can access a page or service that is IP protected.
 *
 * @param string $subpackage The name of the subpackage whose config will be checked.
 * @return bool A value of true if ip validation is turned off or the current user is on the correct network,
 *              a value of false otherwise.
 */
function validate_ip($subpackage) {
    return !get_config('local_signout', "{$subpackage}_form_ipenabled")
               || $_SERVER['REMOTE_ADDR'] === get_config('local_signout', 'school_ip');
}

/**
 * ====================================
 * URL Parameter Querying Abstractions.
 * ====================================
 */

/**
 * Determines the date to be selected which corresponds to an existing off-campus signout record.
 *
 * The priorities of this function are as follows:
 * 1) An id specified as a 'date' GET parameter.
 * 2) The current or date.
 * If there is not an off-campus signout record asseociated with the selected date, an empty string will be returned.
 *
 * @return string The timestamp of midnight on the desired date.
 */
function get_param_current_date_off_campus() {
    global $DB;
    $timestamp = get_param_current_date();
    $startdate = generate_datetime($timestamp);
    $enddate = clone $startdate;
    $enddate->modify('+1 day');
    return $DB->record_exists_sql(
        "SELECT id
         FROM {local_signout_off_campus}
         WHERE deleted = 0 AND departure_time > ? AND departure_time < ?",
        array($startdate->getTimestamp(), $enddate->getTimestamp())
    ) ? $timestamp : '';
}

/**
 * Determines the date to be selected which corresponds to an existing on-campus signout record.
 *
 * The priorities of this function are as follows:
 * 1) An id specified as a 'date' GET parameter.
 * 2) The current or date.
 * If there is not an on-campus signout record asseociated with the selected date, an empty string will be returned.
 *
 * @return string The timestamp of midnight on the desired date.
 */
function get_param_current_date_on_campus() {
    global $DB;
    $timestamp = get_param_current_date();
    $startdate = generate_datetime($timestamp);
    $enddate = clone $startdate;
    $enddate->modify('+1 day');
    return $DB->record_exists_sql(
        "SELECT id
         FROM {local_signout_on_campus}
         WHERE deleted = 0 AND time_created > ? AND time_created < ?", array($startdate->getTimestamp(), $enddate->getTimestamp())
    ) ? $timestamp : '';
}

/**
 * =========================================
 * Database Query for Record List Functions.
 * =========================================
 */

/**
 * Queries the database to create a list of all the students who have sufficient permissions to participate in off-campus signout.
 * Students are permitted to participate in off-campus signout if their grade is 11 or 12.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_off_campus_permitted_student_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND (s.grade = 11 OR s.grade = 12)
         ORDER BY name"
    );
    return convert_student_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who have sufficient permissions to be another student's passenger.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_permitted_passenger_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
                                         LEFT JOIN {local_mxschool_permissions} p ON s.userid = p.userid
         WHERE u.deleted = 0 AND (s.grade = 11 OR s.grade = 12) AND p.may_ride_with IS NOT NULL AND p.may_ride_with <> 'Over 21'
         ORDER BY name"
    );
    return convert_student_records_to_list($students);
}

/**
 * Queries the database to create a list of all off-campus signout driver records.
 *
 * @return array The drivers as offcampusid => name, ordered alphabetically by name.
 */
function get_permitted_driver_list() {
    global $DB;
    $drivers = $DB->get_records_sql(
        "SELECT oc.id, u.id AS userid, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_signout_off_campus} oc LEFT JOIN {user} u ON oc.userid = u.id
                                            LEFT JOIN {local_mxschool_permissions} p ON oc.userid = p.userid
         WHERE oc.deleted = 0 AND u.deleted = 0 AND oc.type = 'Driver' AND p.may_drive_passengers = 'Yes'
         ORDER BY name ASC, oc.time_modified DESC"
    );
    foreach ($drivers as $driver) {
        $driver->value = format_student_name($driver->userid);
    }
    return convert_records_to_list($drivers);
}

/**
 * Queries the database to create a list of currently available drivers.
 * Drivers are defined as available if today is their departure day and they have not signed in.
 *
 * @param int $ignore The user id of a student to ignore (intended to be used to ignore the current student).
 * @return array The drivers as offcampusid => name, ordered alphabetically by name.
 */
function get_current_driver_list($ignore = 0) {
    global $DB;
    $window = get_config('local_signout', 'off_campus_trip_window');
    $time = generate_datetime("-{$window} minutes");
    $drivers = $DB->get_records_sql(
        "SELECT oc.id, u.id AS userid, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_signout_off_campus} oc LEFT JOIN {user} u ON oc.userid = u.id
                                            LEFT JOIN {local_mxschool_permissions} p ON oc.userid = p.userid
         WHERE oc.deleted = 0 AND u.deleted = 0 AND oc.type = 'Driver' AND oc.time_created >= ? AND oc.sign_in_time IS NULL
                              AND p.may_drive_passengers = 'Yes' AND u.id <> ?
                              AND NOT EXISTS (SELECT id FROM {local_signout_off_campus} WHERE driverid = oc.id AND userid = ?)
         ORDER BY name ASC, oc.time_modified DESC", array($time->getTimestamp(), $ignore, $ignore)
    );
    foreach ($drivers as $driver) {
        $driver->value = format_student_name($driver->userid);
    }
    return convert_records_to_list($drivers);
}

/**
 * Queries the database to create a list of all the students who have sufficient permissions to participate in on-campus signout.
 * Students are permitted to participate in on-campus signout if they are a boarder.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_on_campus_permitted_student_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND s.boarding_status = 'Boarder'
         ORDER BY name"
    );
    return convert_student_records_to_list($students);
}

/**
 * Queries the database to create a list of all faculty who are able to approve off-campus signout.
 *
 * @return array The faculty who are able to approve off-campus signout as userid => name, ordered alphabetically by faculty name.
 */
function get_approver_list() {
    global $DB;
    $faculty = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS value FROM {local_mxschool_faculty} f
         LEFT JOIN {user} u ON f.userid = u.id
         WHERE u.deleted = 0 AND f.may_approve_signout = 1
         ORDER BY value"
    );
    return convert_records_to_list($faculty);
}

/**
 * Creates a list of the types of off-campus signout which a specified student has the permissions to perform.
 *
 * @param int $userid The user id of the student.
 * @return array The types of off-campus signout which the student is allowed to perform.
 */
function get_off_campus_type_list($userid = 0) {
    global $DB;
    $types = array('Driver', 'Passenger', 'Parent', 'Other');
    $record = $DB->get_record_sql(
        "SELECT p.may_drive_to_town AS maydrive, p.may_ride_with AS mayridewith, s.boarding_status AS boardingstatus
         FROM {local_mxschool_student} s LEFT JOIN {local_mxschool_permissions} p ON p.userid = s.userid
         WHERE s.userid = ?", array('userid' => $userid)
    );
    if ($record) {
        if ($record->maydrive === 'No' || $record->boardingstatus !== 'Day') {
            unset($types[array_search('Driver', $types)]);
        }
        if (empty($record->mayridewith) || $record->mayridewith === 'Over 21') {
            unset($types[array_search('Passenger', $types)]);
        }
        $types = array_values($types); // Reset the keys so that [0] can be the default option.
    }
    return $types;
}

/**
 * Queries the database to create a list of all locations which are available to a student for on-campus signout.
 *
 * @param int $grade The grade of the student. A value of 0 indicates that all locations should be returned.
 * @return array The locations which are available to a student of the specified grade for on-campus signout.
 */
function get_on_campus_location_list($grade = 12) {
    global $DB;
    $timestamp = generate_datetime('midnight')->getTimestamp(); // Set to midnight to avoid an off-by-one issue on the end date.
    $locations = $DB->get_records_sql(
        "SELECT id, name AS value
         FROM {local_signout_location} l
         WHERE l.deleted = 0 AND l.grade <= ? AND l.enabled = 1 AND (l.start_date IS NULL OR l.start_date <= ?)
                             AND (l.end_date IS NULL OR l.end_date >= ?)
         ORDER BY value", array($grade, $timestamp, $timestamp)
    );
    return convert_records_to_list($locations);
}

/**
 * Queries the database to create a list of all the dates for which there are off-campus signout records.
 *
 * @return array The dates for which there are off-campus signout records as timestamp => date (mm/dd/yy),
 *               in descending order by date.
 */
function get_off_campus_date_list() {
    global $DB;
    $list = array();
    $records = $DB->get_records_sql(
        "SELECT oc.id, oc.departure_time AS signoutdate
         FROM {local_signout_off_campus} oc LEFT JOIN {user} u ON oc.userid = u.id
         WHERE oc.deleted = 0 AND u.deleted = 0 AND oc.type <> 'Passenger'
         ORDER BY departure_time DESC"
    );
    if ($records) {
        foreach ($records as $record) {
            $date = generate_datetime($record->signoutdate);
            $date->modify('midnight');
            if (!array_key_exists($date->getTimestamp(), $list)) {
                $list[$date->getTimestamp()] = $date->format('m/d/y');
            }
        }
    }
    return $list;
}

/**
 * Queries the database to create a list of all the dates for which there are on-campus signout records.
 *
 * @return array The dates for which there are on-campus signout records as timestamp => date (mm/dd/yy),
 *               in descending order by date.
 */
function get_on_campus_date_list() {
    global $DB;
    $list = array();
    $records = $DB->get_records_sql(
        "SELECT oc.id, oc.time_created AS signoutdate
         FROM {local_signout_on_campus} oc LEFT JOIN {user} u ON oc.userid = u.id
                                           LEFT JOIN {local_signout_location} l ON oc.locationid = l.id
                                           LEFT JOIN {user} c ON oc.confirmerid = c.id
         WHERE oc.deleted = 0 AND u.deleted = 0 AND (oc.locationid = -1 OR l.deleted = 0)
                              AND (oc.confirmerid IS NULL OR c.deleted = 0)
         ORDER BY signoutdate DESC"
    );
    if ($records) {
        foreach ($records as $record) {
            $date = generate_datetime($record->signoutdate);
            $date->modify('midnight');
            if (!array_key_exists($date->getTimestamp(), $list)) {
                $list[$date->getTimestamp()] = $date->format('m/d/y');
            }
        }
    }
    return $list;
}

/**
 * ============================================
 * Miscellaneous Subpackage-Specific Functions.
 * ============================================
 */

/**
 * Retrieves the destination and departure time fields from a off-campus singout driver record.
 *
 * @param int $offcampusid The id of driver record.
 * @return stdClass Object with properties destination, departurehour, departureminutes, and departureampm.
 * @throws coding_exception If the off-campus signout record is not a driver record.
 */
function get_driver_inheritable_fields($offcampusid) {
    global $DB;
    $record = $DB->get_record('local_signout_off_campus', array('id' => $offcampusid));
    if (!$record || $record->type !== 'Driver') {
        throw new coding_exception("off-campus signout record with id {$offcampusid} is not a driver");
    }
    $result = new stdClass();
    $result->destination = $record->destination;
    $departuretime = generate_datetime($record->departure_time);
    $result->departurehour = $departuretime->format('g');
    $minute = $departuretime->format('i');
    $minute -= $minute % 15;
    $result->departureminute = "{$minute}";
    $result->departureampm = $departuretime->format('A') === 'PM';
    return $result;
}

/**
 * Determines the timestamp at which an off-campus record can no longer be edited by the student who created it.
 *
 * @param int $timecreated The timestamp of the time when the record was created.
 * @return int The timestamp at which the student can no longer edit the record.
 */
function get_edit_cutoff($timecreated) {
    $editwindow = get_config('local_signout', 'off_campus_edit_window');
    $editcutoff = generate_datetime($timecreated);
    $editcutoff->modify("+{$editwindow} minutes");
    return $editcutoff->getTimestamp();
}

/**
 * Queries the database to determine where the currently logged-in student is signed out to.
 *
 * The priorities of this function are as follows:
 * 1) The student's most recent on-campus record which has not been signed in, if the student may access on-campus signout.
 * 2) The student's least recent off-campus record which has not been signed in, if the student may access off-campus signout.
 * If neither of these options exist, a value of false will be returned.
 *
 * NOTE: It should not be possible for a student to have an off-campus record and another signout record active simultaneously,
 *       but this function is designed to handle such a scenario should it occur.
 *
 * @return stdClass|bool Object with properties id, type, location, and timecreated if the student has an active signout record,
 *                       otherwise a value of false.
 */
function get_user_current_signout() {
    global $DB, $USER;
    if (!user_is_student()) {
        return false;
    }
    $result = new stdClass();
    if (student_may_access_on_campus_signout($USER->id)) {
        $record = $DB->get_record_sql(
            "SELECT oc.id, l.name AS location, oc.other, oc.time_created AS timecreated
             FROM {local_signout_on_campus} oc LEFT JOIN {local_signout_location} l ON oc.locationid = l.id
             WHERE oc.userid = ? AND oc.sign_in_time IS NULL AND oc.deleted = 0
             ORDER BY oc.time_created DESC", array($USER->id), IGNORE_MULTIPLE
        );
        if ($record) {
            $result->id = $record->id;
            $result->type = 'on_campus';
            $result->location = $record->location ?? $record->other;
            $result->timecreated = $record->timecreated;
            return $result;
        }
    }
    if (student_may_access_off_campus_signout($USER->id)) {
        $record = $DB->get_record_sql(
            "SELECT oc.id, d.destination, oc.time_created AS timecreated
             FROM {local_signout_off_campus} oc LEFT JOIN {local_signout_off_campus} d ON oc.driverid = d.id
             WHERE oc.userid = ? AND oc.sign_in_time IS NULL AND oc.deleted = 0
             ORDER BY oc.time_created", array($USER->id), IGNORE_MULTIPLE
        );
        if ($record) {
            $result->id = $record->id;
            $result->type = 'off_campus';
            $result->location = $record->destination;
            $result->timecreated = $record->timecreated;
            return $result;
        }
    }
    return false;
}

/**
 * Signs in the appropriate record(s) for the currently logged-in student.
 * Sign in will fail if the student is not connected to the Middlesex network and the config flags this as necessary.
 *
 * The function will sign in one of the following options:
 * 1) All of the student's on-campus records which have not been signed in, if the student may access on-campus signout.
 * 2) The student's least recent off-campus record which has not been signed in, if the student may access off-campus signout.
 * If neither of these options exist, a value of false will be returned.
 *
 * NOTE: It should not be possible for a student to have an off-campus record and another signout record active simultaneously,
 *       but this function is designed to handle such a scenario should it occur.
 *
 * @return bool A value of true if sign in occurs successfully, a value of false if no records are found to sign in.
 */
function sign_in_user() {
    global $DB, $USER;
    $currentsignout = get_user_current_signout();
    if (!$currentsignout || !validate_ip($currentsignout->type)) {
        return false;
    }
    if ($currentsignout->type === 'on_campus') {
        $records = $DB->get_records('local_signout_on_campus', array(
            'userid' => $USER->id, 'sign_in_time' => null, 'deleted' => 0
        ));
        if (!$records) {
            return false;
        }
        foreach ($records as $record) {
            $record->sign_in_time = $record->time_modified = time();
            $DB->update_record('local_signout_on_campus', $record);
        }
        \local_mxschool\event\record_updated::create(array('other' => array(
            'page' => get_string('on_campus_form', 'local_signout')
        )))->trigger();
    } else {
        $record = $DB->get_record_sql(
            "SELECT *
             FROM {local_signout_off_campus} oc
             WHERE oc.userid = ? AND oc.sign_in_time IS NULL AND oc.deleted = 0
             ORDER BY oc.time_created", array($USER->id), IGNORE_MULTIPLE
        );
        if (!$record) {
            return false;
        }
        $record->sign_in_time = $record->time_modified = time();
        $DB->update_record('local_signout_off_campus', $record);
        \local_mxschool\event\record_updated::create(array('other' => array(
            'page' => get_string('off_campus_form', 'local_signout')
        )))->trigger();
    }
    return true;
}
