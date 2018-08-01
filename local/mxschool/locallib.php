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
 * Local functions for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/classes/event/page_viewed.php');
require_once(__DIR__.'/classes/event/record_updated.php');
require_once(__DIR__.'/classes/event/record_deleted.php');

/**
 * Sets the url, title, heading, context, layout, and navbar of a page as well as logging that the page was visited.
 *
 * @param string $url The url for the page.
 * @param string $title The title and heading for the page.
 * @param array $parents Parent pages to be displayed as linkes in the navbar represented as $displaytext => $url.
 */
function setup_mxschool_page($url, $title, $parents) {
    global $PAGE;
    setup_generic_page($url, $title);
    $PAGE->set_pagelayout('incourse');
    foreach ($parents as $display => $parenturl) {
        $PAGE->navbar->add($display, new moodle_url($parenturl));
    }
    $PAGE->navbar->add($title);
}

/**
 * Sets the url, title, heading, and context of a page as well as logging that the page was visited.
 *
 * @param string $url The url for the page.
 * @param string $title The title and heading for the page.
 */
function setup_generic_page($url, $title) {
    global $PAGE;
    $PAGE->set_url(new moodle_url($url));
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title($title);
    $PAGE->set_heading($title);

    \local_mxschool\event\page_viewed::create(array('other' => array('page' => $title)))->trigger();
}

/**
 * Redirects the user with a notification and logs the event of the redirect.
 *
 * @param moodle_url $url The url to redirect to.
 * @param string $notification The localized text to display on the notification.
 * @param string $type The type of the event to be logged - either create, update, or delete.
 */
function logged_redirect($url, $notification, $type, $success = true) {
    global $PAGE;
    if ($success) {
        switch($type) {
            case 'create':
                \local_mxschool\event\record_created::create(array('other' => array('page' => $PAGE->title)))->trigger();
                break;
            case 'update':
                \local_mxschool\event\record_updated::create(array('other' => array('page' => $PAGE->title)))->trigger();
                break;
            case 'delete':
                \local_mxschool\event\record_deleted::create(array('other' => array('page' => $PAGE->title)))->trigger();
                break;
            default:
                debugging("Invalid event type: {$type}", DEBUG_DEVELOPER);
        }
    }
    redirect(
        $url, $notification, null, $success ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_WARNING
    );
}

/**
 * Determines the redirect url for a form when there is no referer or the state is invalid.
 *
 * @param array $parents Array of parent pages with values which are relative urls.
 * @return moodle_url The url for the form to redirect to if there is no referer.
 */
function get_redirect($parents) {
    return new moodle_url(
        has_capability('moodle/site:config', context_system::instance())
        ? $parents[array_keys($parents)[count($parents) - 1]] : '/my'
    );
}

/**
 * Generates and performs an SQL query to retrieve a record from the database.
 *
 * @param array $queryfields The fields to query - must be organized as [table => [abbreviation, join, fields => [header => name]]].
 * @param string $where A where clause (without the WHERE keyword).
 * @param array $params Any parameters for the where clause.
 * @return stdClass The record object.
 */
function get_record($queryfields, $where, $params = array()) {
    global $DB;
    $selectarray = array();
    $fromarray = array();
    foreach ($queryfields as $table => $tablefields) {
        $abbreviation = $tablefields['abbreviation'];
        foreach ($tablefields['fields'] as $header => $name) {
            if (is_numeric($header)) {
                $selectarray[] = "{$abbreviation}.{$name}";
            } else {
                $selectarray[] = "{$abbreviation}.{$header} AS {$name}";
            }
        }
        if (!isset($tablefields['join'])) {
            $fromarray[] = "{{$table}} {$abbreviation}";
        } else {
            $join = $tablefields['join'];
            $fromarray[] = "LEFT JOIN {{$table}} {$abbreviation} ON {$join}";
        }
    }
    $select = implode(', ', $selectarray);
    $from = implode(' ', $fromarray);
    return $DB->get_record_sql("SELECT $select FROM $from WHERE $where", $params);
}

/**
 * Updates a record in the database or inserts it if it doesn't already exist.
 *
 * @param array $queryfields The fields to query - must be organized as [table => [abbreviation, join, fields => [header => name]]].
 * @param stdClass $data The new data to update the database with.
 * @return int|array The id of the updated or inserted record
 *                   - if more than one record is updated all the affected ids will be returned in the provided order.
 */
function update_record($queryfields, $data) {
    global $DB;
    $ids = array();
    foreach ($queryfields as $table => $tablefields) {
        $record = new stdClass();
        foreach ($tablefields['fields'] as $header => $name) {
            if (is_numeric($header)) {
                $header = $name;
            }
            $record->$header = $data->$name;
        }
        if ($record->id) {
            $DB->update_record($table, $record);
            $ids[] = $record->id;
        } else {
            $ids[] = $DB->insert_record($table, $record);
        }
    }
    return count($ids) === 1 ? $ids[0] : $ids;
}

/**
 * Updates a notification record in the database or inserts it if it doesn't already exist.
 *
 * @param string $class The class identifier of the notification.
 * @param string $subject The new subject for the notification.
 * @param string $body The new body for the notification.
 */
function update_notification($class, $subject, $body) {
    global $DB;
    $record = new stdClass();
    $record->class = $class;
    $record->subject = $subject;
    $record->body_html = $body['text'];
    $id = $DB->get_field('local_mxschool_notification', 'id', array('class' => $class));
    if ($id) {
        $record->id = $id;
        $DB->update_record('local_mxschool_notification', $record);
    } else {
        $DB->insert_record('local_mxschool_notification', $record);
    }
}

/**
 * Determines whether the current user is a student.
 * If this fuction returns true, it is safe to use $USER->id to reference the current student's user id.
 *
 * @return bool Whether the user is a student.
 */
function user_is_student() {
    global $USER, $DB;
    return $DB->record_exists('local_mxschool_student', array('userid' => $USER->id));
}

/**
 * Determines whether a specified user is a student who is permitted to access weekend forms.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access weekend forms.
 */
function student_may_access_weekend($userid) {
    return array_key_exists($userid, get_boarding_student_list());
}

/**
 * Determines whether a specified user is a student who is permitted to access eSignout.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access eSignout.
 */
function student_may_access_esignout($userid) {
    return get_config('local_mxschool', 'esignout_form_enabled') && array_key_exists($userid, get_esignout_student_list());
}

/**
 * Determines whether a specified user is a student who is permitted to access the advisor selectino form.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access the advisor selection form.
 */
function student_may_access_advisor_selection($userid) {
    $start = (int) get_config('local_mxschool', 'advisor_form_start_date') ?: get_config('local_mxschool', 'dorms_open_date');
    $stop = (int) get_config('local_mxschool', 'advisor_form_stop_date') ?: get_config('local_mxschool', 'dorms_close_date');
    return $start && $stop && time() > $start && time() < $stop
           && array_key_exists($userid, get_student_with_advisor_form_enabled_list());
}
/**
 * Determines whether a specified user is a student who is permitted to access the rooming form.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access the rooming form.
 */
function student_may_access_rooming($userid) {
    $start = (int) get_config('local_mxschool', 'rooming_form_start_date') ?: get_config('local_mxschool', 'dorms_open_date');
    $stop = (int) get_config('local_mxschool', 'rooming_form_stop_date') ?: get_config('local_mxschool', 'dorms_close_date');
    return $start && $stop && time() > $start && time() < $stop && array_key_exists($userid, get_boarding_next_year_student_list());
}

/**
 * Determines whether a specified user is a student who is permitted to access the vacation travel form.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access the vacation travel form.
 */
function student_may_access_vacation_travel($userid) {
    $start = (int) get_config('local_mxschool', 'vacation_form_start_date') ?: get_config('local_mxschool', 'dorms_open_date');
    $stop = (int) get_config('local_mxschool', 'vacation_form_stop_date') ?: get_config('local_mxschool', 'dorms_close_date');
    return $start && $stop && time() > $start && time() < $stop && array_key_exists($userid, get_boarding_student_list());
}

/**
 * Determines the dorm id to display for a faculty.
 * The priorities of this function are as follows:
 * 1) An id specified as a 'dorm' GET parameter.
 * 2) The dorm of the currently logged in faculty member, if it exists.
 * 3) An empty string.
 *
 * @return string The dorm id or an empty string, as specified.
 */
function get_param_faculty_dorm() {
    global $DB, $USER;
    return isset($_GET['dorm']) ? $_GET['dorm']
    : $DB->get_field('local_mxschool_faculty', 'dormid', array('userid' => $USER->id)) ?: '';
}

/**
 * Determines the date to be selected.
 * The priorities of this function are as follows:
 * 1) An id specified as a 'date' GET parameter.
 * 2) The current or date.
 *
 * @return string The timestamp of the midnight on the desired date.
 */
function get_param_current_date() {
    return isset($_GET['date']) ? $_GET['date']
    : (new DateTime('midnight', core_date::get_server_timezone_object()))->getTimestamp();
}

/**
 * Determines the date to be selected which corresponds to an existing eSignout record.
 * The priorities of this function are as follows:
 * 1) An id specified as a 'date' GET parameter.
 * 2) The current or date.
 * If there is not an eSignout record asseociated with the selected date, an empty string will be returned.
 *
 * @return string The timestamp of the midnight on the desired date.
 */
function get_param_current_date_esignout() {
    global $DB;
    $timestamp = (int)get_param_current_date();
    $startdate = new DateTime('now', core_date::get_server_timezone_object());
    $startdate->setTimestamp($timestamp);
    $enddate = clone $startdate;
    $enddate->modify('+1 day');
    return $DB->record_exists_sql(
        "SELECT * FROM {local_mxschool_esignout} WHERE deleted = 0 AND departure_time > ? AND departure_time < ?",
        array($startdate->getTimestamp(), $enddate->getTimestamp())
    ) ? $timestamp : '';
}

/**
 * Determines the weekend id to be selected.
 * The priorities of this function are as follows:
 * 1) An id specified as a 'weekend' GET parameter.
 * 2) The current or upcoming weekend (resets Wednesday 0:00:00).
 * 3) The next weekend with a record in the database.
 * 4) Defaults to a value of '0'.
 *
 * @return string The weekend id, as specified.
 */
function get_param_current_weekend() {
    global $DB;
    if (isset($_GET['weekend'])) {
        return $_GET['weekend'];
    }
    $starttime = get_config('local_mxschool', 'dorms_open_date');
    $endtime = get_config('local_mxschool', 'dorms_close_date');
    $date = new DateTime('now', core_date::get_server_timezone_object());
    $date->modify('-2 days'); // Map 0:00:00 on Wednesday to 0:00:00 on Monday.
    $date->modify('Sunday this week');
    $timestamp = $date->getTimestamp();
    if ($timestamp >= $starttime && $timestamp < $endtime) {
        $weekend = $DB->get_field('local_mxschool_weekend', 'id', array('sunday_time' => $timestamp));
        if ($weekend) {
            return $weekend;
        }
    }
    $weekend = $DB->get_field_sql(
        "SELECT id FROM {local_mxschool_weekend}
         WHERE sunday_time >= ? AND sunday_time >= ? AND sunday_time < ? ORDER BY sunday_time",
        array($timestamp, $starttime, $endtime), IGNORE_MULTIPLE
    );
    if ($weekend) {
        return $weekend;
    }
    return '0';
}

/**
 * Determines the semester ('1' or '2') to be selected.
 * The priorities of this function are as follows:
 * 1) A value specified as a 'semester' GET parameter.
 * 2) The current semster.
 * 3) The first semester if before the dorms open date; the second semester if after the dorms close date.
 *
 * @return string The semester, as specified.
 */
function get_param_current_semester() {
    return isset($_GET['semester']) && ($_GET['semester'] === '1' || $_GET['semester'] === '2') ? $_GET['semester']
    : get_current_semester();
}

/**
 * Determines the current semester ('1' or '2').
 * This is determined to be the '1' if the current date is before the start date of the second semester and '2' if it is after.
 *
 * @return string The semester, as specified.
 */
function get_current_semester() {
    $semesterdate = get_config('local_mxschool', 'second_semester_start_date');
    $date = new DateTime('now', core_date::get_server_timezone_object());
    return $date->getTimestamp() < $semesterdate ? '1' : '2';
}

/**
 * Sets the data for a time selector based on a timstamp and a step.
 *
 * @param stdClass $data The data object.
 * @param string $prefix A prefix for the properties to be set.
 *                       - The properties set will be "{$prefix}_time_hour", "{$prefix}_time_minute", and "{$prefix}_time_ampm".
 *                       - The timestamp used will be from "{$prefix}_date".
 * @param int $step An increment indicating the available minute values.
 */
function generate_time_selector_fields(&$data, $prefix, $step = 1) {
    $time = new DateTime('now', core_date::get_server_timezone_object());
    $time->setTimestamp($data->{"{$prefix}_date"});
    $data->{"{$prefix}_time_hour"} = $time->format('g');
    $minute = $time->format('i');
    $data->{"{$prefix}_time_minute"} = $minute - $minute % $step;
    $data->{"{$prefix}_time_ampm"} = $time->format('A') === 'PM';
}

/**
 * Generates a timestamp as the result of a time selector.
 *
 * @param stdClass|array $data The data object.
 * @param string $prefix A prefix for the properties to access.
 *                       - The properties used will be "{$prefix}_date", "{$prefix}_time_hour", "{$prefix}_time_minute", and
 *                         "{$prefix}_time_ampm".
 * @param int A timestamp for the current date.
 * @return int The resulting timestamp.
 */
function generate_timestamp($data, $prefix) {
    if (is_array($data)) {
        $data = (object) $data;
    }
    $time = new DateTime('now', core_date::get_server_timezone_object());
    $time->setTimestamp($data->{"{$prefix}_date"});
    $time->setTime($data->{"{$prefix}_time_hour"} % 12 + $data->{"{$prefix}_time_ampm"} * 12, $data->{"{$prefix}_time_minute"});
    return $time->getTimestamp();
}

/**
 * Converts an array of objects with properties id and name to an array with form id => name.
 *
 * @param array $records The record objects to convert.
 * @return array The same data in the form id => name.
 */
function convert_records_to_list($records) {
    $list = array();
    if (is_array($records)) {
        foreach ($records as $record) {
            $list[$record->id] = $record->name.(
                !empty($record->alternatename) && $record->alternatename !== $record->firstname ? " ({$record->alternatename})" : ''
            );
        }
    }
    return $list;
}

/**
 * Queries the database to create a list of all the students.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_student_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id WHERE u.deleted = 0 ORDER BY name"
    );
    return convert_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who are boarders.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_boarding_student_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND s.boarding_status = 'Boarder' ORDER BY name"
    );
    return convert_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who will be boarders next year.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_boarding_next_year_student_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND s.grade <> 12 AND s.boarding_status_next_year = 'Boarder' ORDER BY name"
    );
    return convert_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who are in a specified dorm.
 *
 * @param int $dorm the id of the desired dorm.
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_dorm_student_list($dorm) {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND s.dormid = $dorm ORDER BY name"
    );
    return convert_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who have a registered license.
 * Only day students should fit this criterium.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_licensed_student_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         LEFT JOIN {local_mxschool_permissions} p ON s.userid = p.userid
         WHERE u.deleted = 0 AND p.license_date IS NOT NULL ORDER BY name"
    );
    return convert_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who have sufficient permissions to participate in eSignout.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_esignout_student_list() {
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
function get_passenger_list() {
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
 * Queries the database to create a list of all eSignout driver records.
 *
 * @return array The drivers as esignoutid => name, ordered alphabetically by name.
 */
function get_all_driver_list() {
    global $DB;
    $drivers = $DB->get_records_sql(
        "SELECT es.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename FROM {local_mxschool_esignout} es
         LEFT JOIN {user} u ON es.userid = u.id LEFT JOIN {local_mxschool_permissions} p ON es.userid = p.userid
         WHERE es.deleted = 0 AND u.deleted = 0 AND es.type = 'Driver' AND p.may_drive_passengers = 'Yes'
         ORDER BY name ASC, es.time_modified DESC"
    );
    return convert_records_to_list($drivers);
}

/**
 * Queries the database to create a list of currently available drivers.
 * Drivers are defined as available if today is their departure day and they have not signed in.
 *
 * @param int $ignore The user id of a student to ignore (intended to be used to ignore the current student).
 * @return array The drivers as esignoutid => name, ordered alphabetically by name.
 */
function get_current_driver_list($ignore = 0) {
    global $DB;
    $window = get_config('local_mxschool', 'esignout_trip_window');
    $time = new DateTime('now', core_date::get_server_timezone_object());
    $time->modify("-{$window} minutes");
    $drivers = $DB->get_records_sql(
        "SELECT es.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename FROM {local_mxschool_esignout} es
         LEFT JOIN {user} u ON es.userid = u.id LEFT JOIN {local_mxschool_permissions} p ON es.userid = p.userid
         WHERE es.deleted = 0 AND u.deleted = 0 AND es.type = 'Driver' AND es.time_created >= ? AND es.sign_in_time IS NULL
         AND p.may_drive_passengers = 'Yes' AND u.id <> ? AND (
             SELECT COUNT(id) FROM {local_mxschool_esignout} WHERE driverid = es.id AND userid = ?
         ) = 0 ORDER BY name ASC, es.time_modified DESC", array($time->getTimestamp(), $ignore, $ignore)
    );
    return convert_records_to_list($drivers);
}

/**
 * Queries the database to create a list of all the students who are required to fill out advisor selection form.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_student_with_advisor_form_enabled_list() {
    global $DB;
    $year = (int)date('Y') - 1;
    $where = get_config('local_mxschool', 'advisor_form_enabled_who') === 'new' ? " s.admission_year = {$year}" : ' s.grade <> 12';
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND$where ORDER BY name"
    );
    return convert_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who have not filled out an advisor selection form.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_student_without_advisor_form_list() {
    global $DB;
    $year = (int)date('Y') - 1;
    $where = get_config('local_mxschool', 'advisor_form_enabled_who') === 'new' ? " s.admission_year = {$year}" : ' s.grade <> 12';
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND (SELECT COUNT(id) FROM {local_mxschool_adv_selection} WHERE userid = s.userid) = 0 AND$where
         ORDER BY name"
    );
    return convert_records_to_list($students);
}

/**
 * Queries the database to create a list of all the new student - advisor pairs.
 * Any student who selected to changed advisors on their advisor selection form will be included.
 *
 * @return array The students and advisors as studentuserid => advisoruserid, ordered alphabetically by student name.
 */
function get_new_student_advisor_pair_list() {
    global $DB;
    $records = $DB->get_records_sql(
        "SELECT u.id, asf.selectedid AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         LEFT JOIN {local_mxschool_adv_selection} asf ON s.userid = asf.userid
         WHERE u.deleted = 0 AND asf.keep_current = 0 AND asf.selectedid <> 0 ORDER BY name"
    );
    return convert_records_to_list($records);
}

/**
 * Queries the database to create a list of all the students who will be boarders next year and have not filled out a rooming form.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_student_without_rooming_form_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND s.grade <> 12 AND s.boarding_status_next_year = 'Boarder' AND (
             SELECT COUNT(id) FROM {local_mxschool_rooming} WHERE userid = s.userid
         ) = 0 ORDER BY name"
    );
    return convert_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who could be dormmates of a specified student.
 *
 * @param int $userid The user id of the student to check against.
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_student_possible_dormmate_list($userid) {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         LEFT JOIN {local_mxschool_student} ss ON ss.userid = ?
         WHERE u.deleted = 0 AND s.grade <> 12 AND s.boarding_status_next_year = 'Boarder'
         AND s.gender = ss.gender AND s.userid <> ss.userid ORDER BY name",
         array($userid)
    );
    return convert_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who could be dormmates of a specified student
 * and are in the same grade as that student.
 *
 * @param int $userid The user id of the student to check against.
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_student_possible_same_grade_dormmate_list($userid) {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         LEFT JOIN {local_mxschool_student} ss ON ss.userid = ?
         WHERE u.deleted = 0 AND s.grade <> 12 AND s.boarding_status_next_year = 'Boarder'
         AND s.grade = ss.grade AND s.gender = ss.gender AND s.userid <> ss.userid ORDER BY name",
         array($userid)
    );
    return convert_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who are boarders and have not filled out a vacation travel form.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_student_without_vacation_travel_form_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND s.boarding_status = 'Boarder' AND (
             SELECT COUNT(id) FROM {local_mxschool_vt_trip} WHERE userid = s.userid
         ) = 0 ORDER BY name"
    );
    return convert_records_to_list($students);
}

/**
 * Queries the database to create a list of all the faculty.
 *
 * @return array The faculty as userid => name, ordered alphabetically by faculty name.
 */
function get_faculty_list() {
    global $DB;
    $faculty = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_faculty} f LEFT JOIN {user} u ON f.userid = u.id WHERE u.deleted = 0 ORDER BY name"
    );
    return convert_records_to_list($faculty);
}

/**
 * Queries the database to create a list of all the available advisors.
 *
 * @return array The available advisors as userid => name, ordered alphabetically by advisor name.
 */
function get_available_advisor_list() {
    global $DB;
    $advisors = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_faculty} f LEFT JOIN {user} u ON f.userid = u.id
         WHERE u.deleted = 0 and f.advisory_available = 1 and f.advisory_closing = 0 ORDER BY name"
    );
    return convert_records_to_list($advisors);
}

/**
 * Queries the database to create a list of all faculty who are able to approve eSignout.
 *
 * @return array The faculty who are able to approve eSignout as userid => name, ordered alphabetically by faculty name.
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
 * Queries the database to create a list of all the available dorms.
 *
 * @return array The available dorms as id => name, ordered alphabetically by dorm name.
 */
function get_dorm_list() {
    global $DB;
    $dorms = $DB->get_records_sql(
        "SELECT id, name FROM {local_mxschool_dorm} WHERE deleted = 0 AND available = 1 ORDER BY name"
    );
    return convert_records_to_list($dorms);
}

/**
 * Queries the database to create a list of all the available boarding dorms.
 *
 * @return array The available boarding dorms as id => name, ordered alphabetically by dorm name.
 */
function get_boarding_dorm_list() {
    global $DB;
    $dorms = $DB->get_records_sql(
        "SELECT id, name FROM {local_mxschool_dorm} WHERE deleted = 0 AND available = 1 AND type = 'Boarding' ORDER BY name"
    );
    return convert_records_to_list($dorms);
}

/**
 * Queries the database to create a list of all the weekends between the dorms open date and the dorms close date.
 *
 * @return array The weekends within the specified bounds as id => date (mm/dd/yy), ordered by date.
 */
function get_weekend_list() {
    global $DB;
    $starttime = get_config('local_mxschool', 'dorms_open_date');
    $endtime = get_config('local_mxschool', 'dorms_close_date');
    $weekends = $DB->get_records_sql(
        "SELECT id, sunday_time FROM {local_mxschool_weekend} WHERE sunday_time >= ? AND sunday_time < ? ORDER BY sunday_time",
        array($starttime, $endtime)
    );
    if ($weekends) {
        foreach ($weekends as $weekend) {
            $time = new DateTime('now', core_date::get_server_timezone_object());
            $time->setTimestamp($weekend->sunday_time);
            $time->modify("-1 day");
            $weekend->name = $time->format('m/d/y');
        }
    }
    return convert_records_to_list($weekends);
}

/**
 * Queries the database to create a list of all the dates for which there are eSignout records.
 *
 * @return array The dates for which there are eSignout records as timestamp => date (mm/dd/yy), in descending order by date.
 */
function get_esignout_date_list() {
    global $DB;
    $list = array();
    $records = $DB->get_records_sql(
        "SELECT es.id, es.departure_time FROM {local_mxschool_esignout} es LEFT JOIN {user} u ON es.userid = u.id
         WHERE es.deleted = 0 AND u.deleted = 0 AND es.type <> 'Passenger' ORDER BY departure_time DESC"
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
 * Creates a list of all the vacation travel types given a filter.
 *
 * @param bool|null $mxtransportation Whether types for mx transportation or non-mx transportaion should be returned.
 * @return array The vacation travel types in an indexed array.
 */
function get_vacation_travel_type_list($mxtransportation = null) {
    return isset($mxtransportation) ? (
        $mxtransportation ? array('Plane', 'Train', 'Bus', 'NYC Direct') : array('Car', 'Plane', 'Train', 'Non-MX Bus')
    ) : array('Car', 'Plane', 'Train', 'Bus', 'NYC Direct', 'Non-MX Bus');
}

/**
 * Queries the database to create a list of all the vacation travel departure sites of a particular type.
 *
 * @param string|null $type The type to filter by, if no type is provided all will be returned.
 * @return array The vacation travel departure sites as id => name, ordered alphabetically by site name.
 */
function get_vacation_travel_departure_sites_list($type = null) {
    global $DB;
    $filter = $type ? "AND type = '{$type}'" : '';
    $sites = $DB->get_records_sql(
        "SELECT id, name FROM {local_mxschool_vt_site} WHERE deleted = 0 AND enabled_departure = 1 {$filter} ORDER BY name"
    );
    $list = convert_records_to_list($sites);
    if (!$type || $type === 'Plane' || $type === 'Train' || $type === 'Bus') {
        $list += array(0 => get_string('vacation_travel_form_departure_dep_site_other', 'local_mxschool'));
    }
    return $list;
}

/**
 * Queries the database to create a list of all the vacation travel return sites of a particular type.
 *
 * @param string $type The type to filter by, if no type is provided all will be returned.
 * @return array The vacation travel return sites as id => name, ordered alphabetically by site name.
 */
function get_vacation_travel_return_sites_list($type = null) {
    global $DB;
    $filter = $type ? "AND type = '{$type}'" : '';
    $sites = $DB->get_records_sql(
        "SELECT id, name FROM {local_mxschool_vt_site} WHERE deleted = 0 AND enabled_return = 1 {$filter} ORDER BY name"
    );
    $list = convert_records_to_list($sites);
    if (!$type || $type === 'Plane' || $type === 'Train' || $type === 'Bus') {
        $list += array(0 => get_string('vacation_travel_form_return_ret_site_other', 'local_mxschool'));
    }
    return $list;
}

/**
 * Adds default weekend records for all Sundays between two timestamps.
 *
 * @param int $starttime The timestamp for the beginning of the range.
 * @param int $endtime The timestamp for the end of the range.
 * @return array The fully populated list of weekend records occuring between the two timestamps, ordered by date.
 */
function generate_weekend_records($starttime, $endtime) {
    global $DB;
    $weekends = $DB->get_records_sql(
        "SELECT sunday_time FROM {local_mxschool_weekend} WHERE sunday_time >= ? AND sunday_time < ?", array($starttime, $endtime)
    );
    $sorted = array();
    foreach ($weekends as $weekend) {
        $sorted[$weekend->sunday_time] = $weekend;
    }
    $date = new DateTime('now', core_date::get_server_timezone_object());
    $date->setTimestamp($starttime);
    $date->modify('Sunday this week');
    while ($date->getTimestamp() < $endtime) {
        if (!isset($sorted[$date->getTimestamp()])) {
            $startdate = clone $date;
            $startdate->modify('-1 day');
            $enddate = clone $date;
            $enddate->modify('+1 day -1 second');
            $newweekend = new stdClass();
            $newweekend->sunday_time = $date->getTimestamp();
            $newweekend->start_time = $startdate->getTimestamp();
            $newweekend->end_time = $enddate->getTimestamp();
            $DB->insert_record('local_mxschool_weekend', $newweekend);
        }
        $date->modify('+1 week');
    }
    return $DB->get_records_sql(
        "SELECT * FROM {local_mxschool_weekend} WHERE sunday_time >= ? AND sunday_time < ? ORDER BY sunday_time",
        array($starttime, $endtime)
    );
}

/**
 * Calculates the number of weekends which a student has used.
 * This number is determined by the number of active weekend forms for the student
 * on open or closed weekends in the specified semester.
 *
 * @param int $userid The userid of the student to query for.
 * @param int $semester The semester to query for.
 * @return int The count of the applicable records.
 */
function calculate_weekends_used($userid, $semester) {
    global $DB;
    $startdate = $semester == 1 ? get_config('local_mxschool', 'dorms_open_date')
                                        : get_config('local_mxschool', 'second_semester_start_date');
    $enddate = $semester == 1 ? get_config('local_mxschool', 'second_semester_start_date')
                                      : get_config('local_mxschool', 'dorms_close_date');
    return $DB->count_records_sql(
        "SELECT COUNT(wf.id) FROM {local_mxschool_student} s
         LEFT JOIN {local_mxschool_weekend_form} wf ON s.userid = wf.userid
         LEFT JOIN {local_mxschool_weekend} w ON wf.weekendid = w.id
         WHERE s.userid = ? AND sunday_time >= ? AND sunday_time < ? AND wf.active = 1 AND (w.type = 'open' OR w.type = 'closed')",
        array($userid, $startdate, $enddate)
    );
}

/**
 * Calculates the number of weekends which a student is allowed for a semester.
 * This number is determined from a lookup table.
 *
 * @param int $userid The userid of the student to query for.
 * @param int $semester The semester to query for.
 * @return int The the number of allowed weekends or 0 if allowed unlimited weekends.
 */
function calculate_weekends_allowed($userid, $semester) {
    global $DB;
    $weekendsallowed = array(
        '9' => array('1' => 4, '2' => 4), '10' => array('1' => 4, '2' => 5),
        '11' => array('1' => 6, '2' => 6), '12' => array('1' => 6, '2' => 0)
    );
    $grade = $DB->get_field('local_mxschool_student', 'grade', array('userid' => $userid));
    return $weekendsallowed[$grade][$semester];
}

/**
 * Creates a list of the types of eSignout which a specified student has the permissions to perform.
 *
 * @param int $userid The user id of the student.
 * @return array The types of eSignout which the student is allowed to perform.
 */
function get_esignout_type_list($userid = 0) {
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
 * Retrieves the destination and departure time fields from a esignout driver record.
 *
 * @param int $esignoutid The id of the driver's record.
 * @return stdClass Object with properties destination, departurehour, departureminutes, and departureampm.
 * @throws coding_exception If the esignout record is not a driver record.
 */
function get_driver_inheritable_fields($esignoutid) {
    global $DB;
    $record = $DB->get_record('local_mxschool_esignout', array('id' => $esignoutid));
    if (!$record || $record->type !== 'Driver') {
        throw new coding_exception('eSignout record is not a driver');
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
 * Signs in an eSignout record and records the timestamp.
 *
 * @param int $esignoutid The id of the record to sign in.
 * @return string The text to display for the sign in time.
 * @throws coding_exception If the esignout record does not exist or is already signed in.
 */
function sign_in_esignout($esignoutid) {
    global $DB;
    $record = $DB->get_record('local_mxschool_esignout', array('id' => $esignoutid));
    if (!$record || $record->sign_in_time) {
        throw new coding_exception('eSignout record doesn\'t exist or is already signed in');
    }
    $record->sign_in_time = time();
    $DB->update_record('local_mxschool_esignout', $record);
    return date('g:i A', $record->sign_in_time);
}

/**
 * Creates a list of all the room types for a particular gender.
 *
 * @param string $gender The gender to check for - either 'M', 'F', or ''.
 * @return array The room types as internal_name => localized_name, in order by type.
 */
function get_roomtype_list($gender = '') {
    $roomtypes = array(
        'Single' => get_string('room_type_single', 'local_mxschool'),
        'Double' => get_string('room_type_double', 'local_mxschool')
    );
    if ($gender !== 'M') {
        $roomtypes['Quad'] = get_string('room_type_quad', 'local_mxschool');
    }
    return $roomtypes;
}
