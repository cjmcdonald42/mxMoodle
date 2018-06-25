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
 * @return int The id of the updated or inserted record,
 */
function update_record($queryfields, $data) {
    global $DB;
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
            return $record->id;
        } else {
            return $DB->insert_record($table, $record);
        }
    }
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
    $record->body_html = $body;
    $id = $DB->get_field('local_mxschool_notification', 'id', array('class' => $class));
    if ($id) {
        $record->id = $id;
        $DB->update_record('local_mxschool_notification', $record);
    } else {
        $DB->insert_record('local_mxschool_notification', $record);
    }
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
 * Queries the database to create a list of all the available dorms.
 *
 * @return array The available dorms as id => name, ordered alphabetically by dorm name.
 */
function get_dorms_list() {
    global $DB;
    $list = array();
    $dorms = $DB->get_records_sql("SELECT id, name FROM {local_mxschool_dorm} WHERE available = 'Yes' ORDER BY name");
    if ($dorms) {
        foreach ($dorms as $dorm) {
            $list[$dorm->id] = $dorm->name;
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
    $list = array();
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id WHERE u.deleted = 0 ORDER BY name"
    );
    if ($students) {
        foreach ($students as $student) {
            $list[$student->id] = $student->name;
        }
    }
    return $list;
}

/**
 * Queries the database to create a list of all the students in a specified dorm.
 *
 * @param int $dorm the id of the desired dorm.
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_students_in_dorm_list($dorm) {
    if (!$dorm) {
        return get_student_list();
    }

    global $DB;
    $list = array();
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name FROM {local_mxschool_student} s
         LEFT JOIN {user} u ON s.userid = u.id WHERE u.deleted = 0 AND s.dormid = $dorm ORDER BY name"
    );
    if ($students) {
        foreach ($students as $student) {
            $list[$student->id] = $student->name;
        }
    }
    return $list;
}

/**
 * Queries the database to create a list of all the students which match a filter.
 *
 * @param int $isboarder 1 to query for boarders, 0 to query for day students, -1 to query for all.
 * @param array $grades The grades to query for.
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_student_with_filer_list($isboarder = -1, $grades = array(9, 10, 11, 12)) {
    global $DB;
    $list = array();
    $gradestrings = array();
    foreach ($grades as $grade) {
        $gradestrings[] = "s.grade = {$grade}";
    }
    $borderstring = $isboarder === 1 ? "s.boarding_status = 'Boarding'" : $isboarder === 0 ? "s.boarding_status = 'Day'" : '';
    $gradestring = implode(" OR ", $gradestrings);
    $wherestrings = array("u.deleted = 0", $gradestring ? "({$gradestring})" : $gradestring, $borderstring);
    $where = implode(" AND ", array_filter($wherestrings));
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id WHERE $where ORDER BY name"
    );
    if ($students) {
        foreach ($students as $student) {
            $list[$student->id] = $student->name;
        }
    }
    return $list;
}

/**
 * Queries the database to create a list of all the faculty.
 *
 * @return array The faculty as userid => name, ordered alphabetically by faculty name.
 */
function get_faculty_list() {
    global $DB;
    $list = array();
    $allfaculty = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_faculty} f LEFT JOIN {user} u ON f.userid = u.id WHERE u.deleted = 0 ORDER BY name"
    );
    if ($allfaculty) {
        foreach ($allfaculty as $faculty) {
            $list[$faculty->id] = $faculty->name;
        }
    }
    return $list;
}

/**
 * Queries the database to create a list of all the available advisors.
 *
 * @return array The available advisors as userid => name, ordered alphabetically by advisor name.
 */
function get_advisor_list() {
    global $DB;
    $list = array();
    $advisors = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_faculty} f LEFT JOIN {user} u ON f.userid = u.id
         WHERE u.deleted = 0 and f.advisory_available = 'Yes' and f.advisory_closing = 'No' ORDER BY name"
    );
    if ($advisors) {
        foreach ($advisors as $advisor) {
            $list[$advisor->id] = $advisor->name;
        }
    }
    return $list;
}

/**
 * Queries the database to create a list of all faculty who are able to approve eSignout.
 *
 * @return array The faculty who are able to approve eSignout as userid => name, ordered alphabetically by faculty name.
 */
function get_approver_list() {
    global $DB;
    $list = array();
    $allfaculty = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name FROM {local_mxschool_faculty} f
         LEFT JOIN {user} u ON f.userid = u.id WHERE u.deleted = 0 and f.may_approve_signout = 'Yes' ORDER BY name"
    );
    if ($allfaculty) {
        foreach ($allfaculty as $faculty) {
            $list[$faculty->id] = $faculty->name;
        }
    }
    return $list;
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
    $list = array();
    $weekends = $DB->get_records_sql(
        "SELECT id, sunday_time FROM {local_mxschool_weekend} WHERE sunday_time >= ? AND sunday_time < ? ORDER BY sunday_time",
        array($starttime, $endtime)
    );
    if ($weekends) {
        foreach ($weekends as $weekend) {
            $time = new DateTime('now', core_date::get_server_timezone_object());
            $time->setTimestamp($weekend->sunday_time);
            $time->modify("-1 day");
            $list[$weekend->id] = $time->format('m/d/y');
        }
    }
    return $list;
}

/**
 * Queries the database to create a list of all the dates for which there are eSignout records.
 *
 * @return array The dates for which there are eSignout records as timestamp => date (mm/dd/yy), in descending order by date.
 */
function get_esignout_dates_list() {
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
 * Queries the database to create a list of currently available drivers.
 * Drivers are defined as available if today is their departure day and they have not signed in.
 *
 * @return array The drivers as esignoutid => name, ordered alphabetically by name.
 */
function get_current_drivers_list() {
    global $DB;
    $list = array();
    $today = new DateTime('midnight', core_date::get_server_timezone_object());
    $drivers = $DB->get_records_sql(
        "SELECT es.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_esignout} es LEFT JOIN {user} u ON es.userid = u.id
         WHERE es.deleted = 0 AND u.deleted = 0 AND es.type = 'Driver' AND es.departure_time >= ? AND es.sign_in_time IS NULL
         ORDER BY name", array($today->getTimestamp())
    );
    if ($drivers) {
        foreach ($drivers as $driver) {
            $list[$driver->id] = $driver->name;
        }
    }
    return $list;
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
        throw new coding_exception('eSignout record not a driver');
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
 * Determines whether the user has a record in the student table of the database.
 *
 * @return bool Whether the user is a student.
 */
function user_is_student() {
    global $USER, $DB;
    return $DB->record_exists('local_mxschool_student', array('userid' => $USER->id));
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
