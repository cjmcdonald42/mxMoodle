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
 * Local library functions for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @author      mxMoodle Development Team
 * @copyright   2021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
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
 * Determines whether a specified user is a student who is permitted to access off-campus signout.
 * Students are permitted to participate in off-campus signout if off-campus signout is enabled.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access off-campus signout.
 */
function student_may_access_off_campus_signout($userid) {
    return get_config('local_signout', 'off_campus_form_enabled') && array_key_exists($userid, get_student_list());
}

/**
 * Determines whether a specified user is a student who is permitted to access on-campus signout.
 * Students are permitted to participate in on-campus signout if on-campus signout is enabled.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access on-campus signout.
 */
function student_may_access_on_campus_signout($userid) {
    return get_config('local_signout', 'on_campus_form_enabled') && array_key_exists($userid, get_student_list());
}

/**
 * Determines whether the current user can access an on_campus page or service that is IP protected.
 *
 * @return bool A value of true if on campus ip validation is turned off or the current user is on the correct network,
 *              a value of false otherwise.
 */
function validate_ip_on_campus() {
    return !get_config('local_signout', "on_campus_ipvalidation_enabled")
        || $_SERVER['REMOTE_ADDR'] === get_config('local_signout', 'school_ip');
}

/**
 * Determines whether the current user can access an off_campus page or service that is IP protected.
 *
 * @return bool A value of true if off campus ip validation is turned off or the current user is on the correct network,
 *              a value of false otherwise.
 */
function validate_ip_off_campus() {
    return !get_config('local_signout', "off_campus_ipvalidation_enabled")
        || $_SERVER['REMOTE_ADDR'] === get_config('local_signout', 'school_ip');
}

/*
 * ====================================
 * URL Parameter Querying Abstractions.
 * ====================================
 */

/**
 * Determines the date to be selected which corresponds to an existing on-campus signout record.
 *
 * The priorities of this function are as follows:
 * 1) An id specified as a 'date' GET parameter.
 * 2) The current date.
 * If there is not an on-campus signout record asseociated with the determined date, an empty string will be returned.
 *
 * @return string The timestamp of midnight on the desired date.
 */
function get_param_current_date_on_campus() {
    $timestamp = get_param_current_date();
    return array_key_exists($timestamp, get_on_campus_date_list()) ? $timestamp : '';
}

/**
 * Determines the date to be selected which corresponds to an existing off-campus signout record.
 *
 * The priorities of this function are as follows:
 * 1) An id specified as a 'date' GET parameter.
 * 2) The current date.
 * If there is not an off-campus signout record asseociated with the determined date, an empty string will be returned.
 *
 * @return string The timestamp of midnight on the desired date.
 */
function get_param_current_date_off_campus() {
    $timestamp = get_param_current_date();
    return array_key_exists($timestamp, get_off_campus_date_list()) ? $timestamp : '';
}

/*
 * =========================================
 * Database Query for Record List Functions.
 * =========================================
 */

/**
 * Queries the database to create a list of all locations which are available to a student for on-campus signout.
 *
 * @param int $userid The user id of the student.
 * @return array The locations which are available to the specified student.
 */
function get_on_campus_location_list($userid = 0) {
    global $DB;
    $record = $DB->get_record(
        'local_mxschool_student', array('userid' => $userid), "grade, boarding_status = 'Day' AS isday"
    );
    if ($record) {
        $allday = $record->isday ? "OR l.all_day = 1" : '';
        $where = "AND (l.grade <= {$record->grade} {$allday})";
    } else {
        $where = '';
    }
    $today = generate_datetime('midnight')->getTimestamp(); // Set to midnight to avoid an off-by-one issue on the end date.
    $locations = $DB->get_records_sql(
        "SELECT id, name AS value
         FROM {local_signout_location} l
         WHERE l.deleted = 0 {$where} AND l.enabled = 1 AND (l.start_date IS NULL OR l.start_date <= ?)
                                      AND (l.end_date IS NULL OR l.end_date >= ?)
         ORDER BY value", array($today, $today)
    );
    return convert_records_to_list($locations);
}

/**
 * Queries the database to create a list of all the students who have sufficient permissions to be another student's passenger.
 *
 * @param int $userid The user id of a student who should not be included.
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_permitted_passenger_list($userid = 0) {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
                                         LEFT JOIN {local_mxschool_permissions} p ON s.userid = p.userid
         WHERE u.deleted = 0 AND u.id <> ? AND s.grade >= 11 AND p.may_drive_with_anyone <> 'No'
         ORDER BY name", array($userid)
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
        "SELECT oc.id, oc.userid, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_signout_off_campus} oc LEFT JOIN {user} u ON oc.userid = u.id
                                            LEFT JOIN {local_signout_type} t ON oc.typeid = t.id
                                            LEFT JOIN {local_mxschool_student} s ON oc.userid = s.userid
                                            LEFT JOIN {local_mxschool_permissions} p ON oc.userid = p.userid
         WHERE oc.deleted = 0 AND u.deleted = 0 AND t.deleted = 0 AND t.required_permissions = 'driver'
                              AND p.may_drive_passengers <> 'No' AND s.grade >= 11
         ORDER BY name ASC, oc.time_modified DESC"
    );
    foreach ($drivers as $driver) {
        $driver->value = format_student_name($driver->userid);
    }
    return convert_records_to_list($drivers);
}


/**
 * Queries the database to create a list of currently available drivers for a given student.
 * Drivers are defined as available if they are allowed to drive passengers, are currently in their trip window,
 * selected the student as a passenger, and have not signed in.
 *
 * @param int $userid The user id of the student to check for.
 * @return array The available drivers as offcampusid => name, ordered alphabetically by name.
 */
function get_current_driver_list($userid = 0) {
    global $DB;
    $window = get_config('local_signout', 'off_campus_trip_window');
    $time = generate_datetime("-{$window} minutes");
    $drivers = $DB->get_records_sql(
        "SELECT oc.id, oc.userid, CONCAT(u.lastname, ', ', u.firstname) AS name, oc.passengers
         FROM {local_signout_off_campus} oc LEFT JOIN {user} u ON oc.userid = u.id
                                            LEFT JOIN {local_signout_type} t ON oc.typeid = t.id
                                            LEFT JOIN {local_mxschool_student} s ON oc.userid = s.userid
                                            LEFT JOIN {local_mxschool_permissions} p ON oc.userid = p.userid
         WHERE oc.deleted = 0 AND u.deleted = 0 AND t.deleted = 0 AND t.required_permissions = 'driver'
                              AND oc.time_created >= ? AND oc.sign_in_time IS NULL AND s.grade >= 11
                              AND p.may_drive_passengers <> 'No' AND NOT EXISTS (
                                  SELECT id
                                  FROM {local_signout_off_campus}
                                  WHERE driverid = oc.id AND userid = ? AND deleted = 0 AND sign_in_time IS NULL
                              )
         ORDER BY oc.time_modified DESC", array($time->getTimestamp(), $userid)
    );
    if ($userid) {
        $drivers = array_filter($drivers, function($driver) use ($userid) {
            return in_array($userid, json_decode($driver->passengers));
        });
    }
    foreach ($drivers as $driver) {
        $driver->value = format_student_name($driver->userid);
    }
    return convert_records_to_list($drivers);
}

/**
 * Queries the database to create a list of all faculty who are able to approve off-campus signout.
 *
 * @return array The faculty who are able to approve off-campus signout as userid => name, ordered alphabetically by faculty name.
 */
function get_approver_list() {
    global $DB;
    $faculty = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS value
         FROM {local_mxschool_faculty} f LEFT JOIN {user} u ON f.userid = u.id
         WHERE u.deleted = 0 AND f.may_approve_signout = 1
         ORDER BY value"
    );
    return convert_records_to_list($faculty);
}

/**
 * Creates a list of the types of off-campus signout which a particular student has the permissions to perform.
 *
 * @param int $userid The user id of the student.
 * @return array The types of off-campus signout which the student has the permissions to perform as id => name,
 *               ordered alphabetically by name.
 */
function get_off_campus_type_list($userid = 0) {
    global $DB;
    $today = generate_datetime('midnight')->getTimestamp(); // Set to midnight to avoid an off-by-one issue on the end date.
    $where = array(
        "deleted = 0", "enabled = 1", "(start_date IS NULL OR start_date <= {$today})",
        "(end_date IS NULL OR end_date >= {$today})"
    );
    if ($userid) {
        $permissions = $DB->get_record_sql(
            "SELECT p.may_drive_passengers AS maydrive, p.may_drive_with_over_21 AS mayridewith21,
		  		p.may_drive_with_anyone AS mayridewithanyone, p.may_use_rideshare AS rideshare, s.grade,
                    s.boarding_status AS boardingstatus
             FROM {local_mxschool_student} s LEFT JOIN {local_mxschool_permissions} p ON p.userid = s.userid
             WHERE s.userid = ?", array('userid' => $userid)
        );
        if (!get_config('local_signout', 'off_campus_form_permissions_active')) {
            $where[] = "required_permissions IS NULL";
        } else {
            if (empty($permissions->maydrive) || $permissions->maydrive === 'No') {
                $where[] = "(required_permissions IS NULL OR required_permissions <> 'driver')";
            }
            if (empty($permissions->mayridewith21) || $permissions->mayridewith21 === 'No') {
                $where[] = "(required_permissions IS NULL OR required_permissions <> 'passenger')";
            }
            if (empty($permissions->rideshare) || $permissions->rideshare === 'No') {
                $where[] = "(required_permissions IS NULL OR required_permissions <> 'rideshare')";
            }
        }
        $where[] = "grade <= {$permissions->grade}";
        $where[] = "(boarding_status = '{$permissions->boardingstatus}' OR boarding_status = 'All')";
    }
    if ($userid && !date_is_in_weekend()) {
        $where[] = "weekend_only = 0";
    }
    $wherestring = implode(' AND ', $where);
    $types = $DB->get_records_select('local_signout_type', $wherestring, null, 'value', 'id, name AS value');
    return convert_records_to_list($types);
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
 * Queries the database to create a list of all the dates for which there are off-campus signout records.
 *
 * @return array The dates for which there are off-campus signout records as timestamp => date (mm/dd/yy),
 *               in descending order by date.
 */
function get_off_campus_date_list() {
    global $DB;
    $list = array();
    $records = $DB->get_records_sql(
        "SELECT oc.id, oc.time_created AS signoutdate
         FROM {local_signout_off_campus} oc LEFT JOIN {user} u ON oc.userid = u.id
                                            LEFT JOIN {local_signout_type} t ON oc.typeid = t.id
         WHERE oc.deleted = 0 AND u.deleted = 0 AND (oc.typeid = -1 OR t.deleted = 0)
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

/*
 * ============================================
 * Miscellaneous Subpackage-Specific Functions.
 * ============================================
 */

 /**
  * Confirms an on-campus signout record and records the timestamp.
  * If the record has already been confirmed, undoes the confirmation.
  *
  * @param int $id The id of the record to confirm.
  * @return stdClass The updated record.
  * @throws coding_exception If the on-campus signout record does not exist or was confirmed by another user.
  */
function confirm_signout($id) {
    global $DB, $USER;
    $record = $DB->get_record('local_signout_on_campus', array('id' => $id));
    if (!$record) {
        throw new coding_exception("on-campus signout record with id {$id} doesn't exist");
    }
    if ($record->confirmation_time) { // Un-confirming.
        if ($record->confirmerid !== $USER->id) {
            throw new coding_exception("on-campus signout record with id {$id} was confirmed by another user");
        }
        $record->confirmation_time = null;
        $record->confirmerid = null;
    } else { // Confirming.
        $record->confirmation_time = $record->time_modified = time();
        $record->confirmerid = $USER->id;
    }
    $DB->update_record('local_signout_on_campus', $record);
    local_mxschool\event\record_updated::create(array('other' => array(
        'page' => get_string('on_campus:duty_report', 'local_signout')
    )))->trigger();
    return $record;
}

/**
 * Retrieves the destination and departure time fields from a off-campus singout driver record.
 *
 * @param int $offcampusid The id of driver record.
 * @return stdClass Object with properties destination, departurehour, departureminutes, and departureampm.
 * @throws coding_exception If the off-campus signout record is not a valid driver record.
 */
function get_driver_inheritable_fields($offcampusid) {
    global $DB;
    if (!array_key_exists($offcampusid, get_current_driver_list())) {
        throw new coding_exception("off-campus signout record with id {$offcampusid} is not a valid driver record");
    }
    $record = $DB->get_record('local_signout_off_campus', array('id' => $offcampusid));
    $departuretime = generate_datetime($record->departure_time);
    return (object) array(
        'destination' => $record->destination,
        'departurehour' => $departuretime->format('g'),
        'departureminute' => (string) ((int) ($departuretime->format('i') / 15) * 15),
        'departureampm' => $departuretime->format('A')
    );
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
 * 1) The student's most recent on-campus record from today which has not been signed in, if on-campus signout is enabled.
 * 2) The student's most recent off-campus record from today which has not been signed in, if off-campus signout is enabled.
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
    $today = generate_datetime('midnight')->getTimestamp();
    if (student_may_access_on_campus_signout($USER->id)) {
        $record = $DB->get_record_sql(
            "SELECT oc.id, l.name AS location, oc.other, oc.time_created AS timecreated
             FROM {local_signout_on_campus} oc LEFT JOIN {local_signout_location} l ON oc.locationid = l.id
             WHERE oc.userid = ? AND oc.deleted = 0 AND (oc.locationid = -1 OR l.deleted = 0) AND oc.sign_in_time IS NULL
                                 AND oc.time_created > ?
             ORDER BY oc.time_created DESC", array($USER->id, $today), IGNORE_MULTIPLE
        );
        if ($record) {
            return (object) array(
                'id' => $record->id,
                'type' => 'on_campus',
                'location' => $record->location ?? $record->other,
                'timecreated' => $record->timecreated
            );
        }
    }
    if (student_may_access_off_campus_signout($USER->id)) {
        $record = $DB->get_record_sql(
            "SELECT oc.id, oc.destination, oc.time_created AS timecreated
             FROM {local_signout_off_campus} oc LEFT JOIN {local_signout_type} t ON oc.typeid = t.id
             WHERE oc.userid = ? AND oc.deleted = 0 AND (oc.typeid = -1 OR t.deleted = 0) AND oc.sign_in_time IS NULL
                                 AND oc.time_created > ?
             ORDER BY oc.time_created DESC", array($USER->id, $today), IGNORE_MULTIPLE
        );
        if ($record) {
            return (object) array(
                'id' => $record->id,
                'type' => 'off_campus',
                'location' => $record->destination,
                'timecreated' => $record->timecreated
            );
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
 * 1) All of the student's on-campus records from today which have not been signed in, if on-campus signout is enabled.
 * 2) All of the student's off-campus records from today which have not been signed in, if off-campus signout is enabled.
 * If neither of these options exist, a value of false will be returned.
 *
 * NOTE: It should not be possible for a student to have an off-campus record and another signout record active simultaneously,
 *       but this function is designed to handle such a scenario should it occur.
 *
 * @return string An error message to be displayed to the user, empty string if no error occurs.
 */
function sign_in_user() {
    global $DB, $USER;
    $currentsignout = get_user_current_signout();
    if (!$currentsignout) {
        get_string('sign_in_button:error:norecord', 'local_signout');
    }
    switch ($currentsignout->type) {
        case 'on_campus':
            if (!validate_ip_on_campus()) {
                $boardingstatus = strtolower($DB->get_field(
                    'local_mxschool_student', 'boarding_status', array('userid' => $USER->id)
                ));
                return get_config('local_signout', "on_campus_signin_ipvalidation_error_{$boardingstatus}");
            }
            break;
        case 'off_campus':
            if (!validate_ip_off_campus()) {
                return get_config('local_signout', 'off_campus_signin_ipvalidation_error');
            }
            break;
        default:
            return get_string('sign_in_button:error:invalidtype', 'local_signout');
    }
    $today = generate_datetime('midnight')->getTimestamp();
    $records = $DB->get_records_select(
        "local_signout_{$currentsignout->type}", 'userid = ? AND sign_in_time IS NULL AND deleted = 0 AND time_created > ?',
        array($USER->id, $today)
    );
    if (!$records) {
        return get_string('sign_in_button:error:invalidrecord', 'local_signout');
    }
    foreach ($records as $record) {
        $record->sign_in_time = $record->time_modified = time();
        $DB->update_record("local_signout_{$currentsignout->type}", $record);
    }
    local_mxschool\event\record_updated::create(array('other' => array(
        'page' => get_string("{$currentsignout->type}:form", 'local_signout')
    )))->trigger();
    return '';
}
