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
 * Local functions for Middlesex School's eSignout Subplugin.
 *
 * @package    local_signout
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/../mxschool/locallib.php');

/**
 * Determines whether a specified user is a student who is permitted to access off-campus signout.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access off-campus signout.
 */
function student_may_access_off_campus_signout($userid) {
    return get_config('local_signout', 'off_campus_form_enabled')
           && array_key_exists($userid, get_off_campus_permitted_student_list());
}

/**
 * Determines the date to be selected which corresponds to an existing off-campus signout record.
 * The priorities of this function are as follows:
 * 1) An id specified as a 'date' GET parameter.
 * 2) The current or date.
 * If there is not an off-campus signout record asseociated with the selected date, an empty string will be returned.
 *
 * @return string The timestamp of the midnight on the desired date.
 */
function get_param_current_date_off_campus() {
    global $DB;
    $timestamp = get_param_current_date();
    $startdate = new DateTime('now', core_date::get_server_timezone_object());
    $startdate->setTimestamp($timestamp);
    $enddate = clone $startdate;
    $enddate->modify('+1 day');
    return $DB->record_exists_sql(
        "SELECT * FROM {local_signout_off_campus} WHERE deleted = 0 AND departure_time > ? AND departure_time < ?",
        array($startdate->getTimestamp(), $enddate->getTimestamp())
    ) ? $timestamp : '';
}

/**
 * Queries the database to create a list of all the students who have sufficient permissions to participate in off-campus signout.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_off_campus_permitted_student_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND (s.grade = 11 OR s.grade = 12) ORDER BY name"
    );
    return convert_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who have sufficient permissions to be another student's passenger.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_permitted_passenger_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         LEFT JOIN {local_mxschool_permissions} p ON s.userid = p.userid
         WHERE u.deleted = 0 AND (s.grade = 11 OR s.grade = 12) AND p.may_ride_with IS NOT NULL AND p.may_ride_with <> 'Over 21'
         ORDER BY name"
    );
    return convert_records_to_list($students);
}

/**
 * Queries the database to create a list of all off-campus signout driver records.
 *
 * @return array The drivers as offcampusid => name, ordered alphabetically by name.
 */
function get_permitted_driver_list() {
    global $DB;
    $drivers = $DB->get_records_sql(
        "SELECT oc.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_signout_off_campus} oc LEFT JOIN {user} u ON oc.userid = u.id
         LEFT JOIN {local_mxschool_permissions} p ON oc.userid = p.userid
         WHERE oc.deleted = 0 AND u.deleted = 0 AND oc.type = 'Driver' AND p.may_drive_passengers = 'Yes'
         ORDER BY name ASC, oc.time_modified DESC"
    );
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
    $time = new DateTime('now', core_date::get_server_timezone_object());
    $time->modify("-{$window} minutes");
    $drivers = $DB->get_records_sql(
        "SELECT oc.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_signout_off_campus} oc LEFT JOIN {user} u ON oc.userid = u.id
         LEFT JOIN {local_mxschool_permissions} p ON oc.userid = p.userid
         WHERE oc.deleted = 0 AND u.deleted = 0 AND oc.type = 'Driver' AND oc.time_created >= ? AND oc.sign_in_time IS NULL
         AND p.may_drive_passengers = 'Yes' AND u.id <> ? AND NOT EXISTS (
             SELECT id FROM {local_signout_off_campus} WHERE driverid = oc.id AND userid = ?
         ) ORDER BY name ASC, oc.time_modified DESC", array($time->getTimestamp(), $ignore, $ignore)
    );
    return convert_records_to_list($drivers);
}

/**
 * Queries the database to create a list of all faculty who are able to approve off-capus signout.
 *
 * @return array The faculty who are able to approve off-campus signout as userid => name, ordered alphabetically by faculty name.
 */
function get_approver_list() {
    global $DB;
    $faculty = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name FROM {local_mxschool_faculty} f
         LEFT JOIN {user} u ON f.userid = u.id WHERE u.deleted = 0 and f.may_approve_signout = 1 ORDER BY name"
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
         FROM {local_mxschool_student} s LEFT JOIN {local_mxschool_permissions} p ON p.userid = s.userid WHERE s.userid = ?",
         array('userid' => $userid)
    );
    if ($record) {
        if ($record->maydrive === 'No' || $record->boardingstatus !== 'Day') {
            unset($types[array_search('Driver', $types)]);
        }
        if ($record->mayridewith === null || $record->mayridewith === 'Over 21') {
            unset($types[array_search('Passenger', $types)]);
        }
        $types = array_values($types); // Reset the keys so that [0] can be the default option.
    }
    return $types;
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
        "SELECT oc.id, oc.departure_time FROM {local_signout_off_campus} oc LEFT JOIN {user} u ON oc.userid = u.id
         WHERE oc.deleted = 0 AND u.deleted = 0 AND oc.type <> 'Passenger' ORDER BY departure_time DESC"
    );
    if ($records) {
        foreach ($records as $record) {
            $date = new DateTime('now', core_date::get_server_timezone_object());
            $date->setTimestamp($record->departure_time);
            $date->modify('midnight');
            if (!array_key_exists($date->getTimestamp(), $list)) {
                $list[$date->getTimestamp()] = $date->format('m/d/y');
            }
        }
    }
    return $list;
}

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
        throw new coding_exception('off-campus signout record is not a driver');
    }
    $result = new stdClass();
    $result->destination = $record->destination;
    $departuretime = new DateTime('now', core_date::get_server_timezone_object());
    $departuretime->setTimestamp($record->departure_time);
    $result->departurehour = $departuretime->format('g');
    $minute = $departuretime->format('i');
    $minute -= $minute % 15;
    $result->departureminute = "{$minute}";
    $result->departureampm = $departuretime->format('A') === 'PM';
    return $result;
}

/**
 * Signs in an off-campus signout record and records the timestamp.
 *
 * @param int $offcampusid The id of the record to sign in.
 * @return string The text to display for the sign in time.
 * @throws coding_exception If the off-campus signout record does not exist or has already been signed in.
 */
function sign_in_off_campus($offcampusid) {
    global $DB;
    $record = $DB->get_record('local_signout_off_campus', array('id' => $offcampusid));
    if (!$record || $record->sign_in_time) {
        throw new coding_exception('off-campus signout record doesn\'t exist or has already been signed in');
    }
    $record->sign_in_time = time();
    $DB->update_record('local_signout_off_campus', $record);
    return date('g:i A', $record->sign_in_time);
}
