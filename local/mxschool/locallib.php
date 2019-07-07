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
 * Local functions for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/classes/event/page_viewed.php');
require_once(__DIR__.'/classes/event/record_updated.php');
require_once(__DIR__.'/classes/event/record_deleted.php');
require_once(__DIR__.'/classes/output/renderable.php');

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
 * Generates a renderable for the index with the specified id in the local_mxschool_subpackage table.
 *
 * @param int $id The id of the subpackage to be indexed.
 * @param bool $heading Whether the localized name of the subpackage should be included as the heading of the index.
 * @return \local_mxschool\output\index The specified index renderable.
 * @throws coding_exception if the subpackage record does not exist.
 */
function generate_index($id, $heading = false) {
    global $DB;
    $record = $DB->get_record('local_mxschool_subpackage', array('id' => $id));
    if (!$record) {
        throw new coding_exception('subpackage record does not exist');
    }
    $links = array();
    foreach (json_decode($record->pages) as $string => $url) {
        $links[get_string(empty($record->subpackage) ? $string : "{$record->subpackage}_{$string}", "local_{$record->package}")]
            = "/local/{$record->package}/{$record->subpackage}/{$url}";
    }
    $headingtext = empty($record->subpackage) ? get_string($record->package, "local_{$record->package}")
        : get_string($record->subpackage, "local_{$record->package}");
    return new \local_mxschool\output\index($links, $heading ? $headingtext : false);
}

/**
 * Outputs a rendered page which indexes a subpackage using the data in the local_mxschool_subpackage table.
 *
 * @param string $subpackage The name of the subpackage to be rendered.
 * @param string $package The package which the specified subpackage belongs to without the 'local' prefix.
 * @throws coding_exception If the specified package, subpackage pair does not exist in the local_mxschool_subpackage table.
 */
function render_index_page($subpackage, $package = 'mxschool') {
    global $DB, $PAGE;
    $id = $DB->get_field('local_mxschool_subpackage', 'id', array('package' => $package, 'subpackage' => $subpackage));
    if (!$id) {
        throw new coding_exception('subpackage record does not exist');
    }

    $url = empty($subpackage) ? "/local/{$package}/index.php" : "/local/{$package}/{$subpackage}/index.php";
    $title = empty($subpackage) ? get_string($package, "local_{$package}") : get_string($subpackage, "local_{$package}");

    setup_generic_page($url, $title);

    $output = $PAGE->get_renderer('local_mxschool');
    echo $output->header();
    echo $output->heading($title);
    echo $output->render(generate_index($id));
    echo $output->footer();
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
        has_capability('moodle/site:config', context_system::instance()) ? $parents[array_keys($parents)[count($parents) - 1]]
            : '/my'
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
            $selectarray[] = is_numeric($header) ? "{$abbreviation}.{$name}" : "{$abbreviation}.{$header} AS {$name}";
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
 * Retrieves the notification record of a particular class from the database.
 * Creates the record if it does not exist already.
 *
 * @param string $emailclass The class of the email to be retrieved.
 * @return stdClass The notification record object.
 */
function get_notification($emailclass) {
    global $DB;
    if (!$DB->record_exists('local_mxschool_notification', array('class' => $emailclass))) {
        $record = new stdClass();
        $record->class = $emailclass;
        $record->body_html = '';
        $DB->insert_record('local_mxschool_notification', $record);
    }
    return $DB->get_record('local_mxschool_notification', array('class' => $emailclass));
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
 * Generates a DateTime object from a time string or timestamp with the user's timezone.
 *
 * @param string|int $time A date/time string in a format accepted by date() (https://www.php.net/manual/en/function.date.php)
 *                         or a timestamp.
 * @return DateTime The DateTime object with the specified time in the user's timezone.
 */
function generate_datetime($time='now') {
    return is_numeric($time) ? (new DateTime('now', core_date::get_user_timezone_object()))->setTimestamp($time)
        : new DateTime($time, core_date::get_server_timezone_object());
}

/**
 * Formats a date/time in a specified format with the user's timezone.
 *
 * @param string $format The format to output the timestamp in a format accepted by date()
 *                       (https://www.php.net/manual/en/function.date.php).
 * @param string|int $time A date/time string in a format accepted by date() (https://www.php.net/manual/en/function.date.php)
 *                         or a timestamp.
 * @return string The specified time in the specified format.
 */
function format_date($format, $time = 'now') {
    return generate_datetime($time)->format($format);
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
    $time = generate_datetime($data->{"{$prefix}_date"});
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
    $data = (object)$data;
    $time = generate_datetime($data->{"{$prefix}_date"});
    $time->setTime($data->{"{$prefix}_time_hour"} % 12 + $data->{"{$prefix}_time_ampm"} * 12, $data->{"{$prefix}_time_minute"});
    return $time->getTimestamp();
}

/**
 * Helper method to convert a timestamp into an object.
 *
 * @param int $timestamp The timestamp to convert.
 * @return stdClass Object with properties year, month, day, hour, minute, ampm.
 */
function enumerate_timestamp($timestamp) {
    $result = new stdClass();
    if ($timestamp) {
        $time = generate_datetime($timestamp);
        $result->year = $time->format('Y');
        $result->month = $time->format('n');
        $result->day = $time->format('j');
        $result->hour = $time->format('g');
        $minute = $time->format('i');
        $minute -= $minute % 15;
        $result->minute = "{$minute}";
        $result->ampm = $time->format('A') === 'PM';
    } else {
        $result->year = '';
        $result->month = '';
        $result->day = '';
        $result->hour = '';
        $result->minute = '';
        $result->ampm = false;
    }
    return $result;
}

/**
 * Converts a boolean value to a 'yes' or 'no' language string.
 *
 * @param bool $boolean The boolean value.
 * @return string The language appropriate 'yes' or 'no' value.
 */
function boolean_to_yes_no($boolean) {
    return $boolean ? get_string('yes') : get_string('no');
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

// /**
//  * Determines whether a specified user is a student who is permitted to access eSignout.
//  *
//  * @param int $id The user id of the student to check.
//  * @return bool Whether the specified student is permitted to access eSignout.
//  */
// function student_may_access_esignout($userid) {
//     return get_config('local_mxschool', 'esignout_form_enabled') && array_key_exists($userid, get_esignout_student_list());
// }

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
    return isset($_GET['dorm']) && is_numeric($_GET['dorm']) ? $_GET['dorm'] : (
        $DB->get_field('local_mxschool_faculty', 'dormid', array('userid' => $USER->id)) ?: ''
    );
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
    return isset($_GET['date']) && is_numeric($_GET['date']) ? $_GET['date'] : generate_datetime('midnight')->getTimestamp();
}

// /**
//  * Determines the date to be selected which corresponds to an existing eSignout record.
//  * The priorities of this function are as follows:
//  * 1) An id specified as a 'date' GET parameter.
//  * 2) The current or date.
//  * If there is not an eSignout record asseociated with the selected date, an empty string will be returned.
//  *
//  * @return string The timestamp of the midnight on the desired date.
//  */
// function get_param_current_date_esignout() {
//     global $DB;
//     $timestamp = get_param_current_date();
//     $startdate = new DateTime('now', core_date::get_server_timezone_object());
//     $startdate->setTimestamp($timestamp);
//     $enddate = clone $startdate;
//     $enddate->modify('+1 day');
//     return $DB->record_exists_sql(
//         "SELECT * FROM {local_mxschool_esignout} WHERE deleted = 0 AND departure_time > ? AND departure_time < ?",
//         array($startdate->getTimestamp(), $enddate->getTimestamp())
//     ) ? $timestamp : '';
// }

/**
 * Determines the weekend id to be selected.
 * The priorities of this function are as follows:
 * 1) An id specified as a 'weekend' GET parameter.
 * 2) The current or upcoming weekend (resets Wednesday 0:00:00).
 * 3) The next weekend with a record in the database.
 * 4) The latest weekend in the database
 *
 * @return string The weekend id, as specified.
 * @throws moodle_exception If are no weekend records in the database between the dorms-open date and dorms-close date configs.
 */
function get_param_current_weekend() {
    global $DB;
    if (
        isset($_GET['weekend']) && is_numeric($_GET['weekend'])
        && $DB->record_exists('local_mxschool_weekend', array('id' => $_GET['weekend']))
    ) {
        return $_GET['weekend'];
    }
    $starttime = get_config('local_mxschool', 'dorms_open_date');
    $endtime = get_config('local_mxschool', 'dorms_close_date');
    $date = generate_datetime('-2 days'); // Map 0:00:00 on Wednesday to 0:00:00 on Monday.
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
    $weekend = $DB->get_field_sql(
        "SELECT id FROM {local_mxschool_weekend}
         WHERE sunday_time >= ? AND sunday_time < ? ORDER BY sunday_time DESC",
        array($starttime, $endtime), IGNORE_MULTIPLE
    );
    if ($weekend) {
        return $weekend;
    }
    throw new moodle_exception('there are no valid weekend records in the database');
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
    return generate_datetime()->getTimestamp() < $semesterdate ? '1' : '2';
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
            $list[$record->id] = $record->name . (
                !empty($record->alternatename) && $record->alternatename !== $record->firstname ? " ({$record->alternatename})" : ''
            );
        }
    }
    return $list;
}

/**
 * Converts an associative array into an array of objects with properties value and text to be used in select elements.
 *
 * @param array $list The associative array to convert.
 * @return array The same data in the form {'value': key, 'text': value}.
 */
function convert_associative_to_object($list) {
    return array_map(function($key, $value) {
        return array('value' => $key, 'text' => $value);
    }, array_keys($list), $list);
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

// /**
//  * Queries the database to create a list of all the students who have sufficient permissions to participate in eSignout.
//  *
//  * @return array The students as userid => name, ordered alphabetically by student name.
//  */
// function get_esignout_student_list() {
//     global $DB;
//     $students = $DB->get_records_sql(
//         "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
//          FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
//          WHERE u.deleted = 0 AND (s.grade = 11 OR s.grade = 12) ORDER BY name"
//     );
//     return convert_records_to_list($students);
// }

// /**
//  * Queries the database to create a list of all the students who have sufficient permissions to be another student's passenger.
//  *
//  * @return array The students as userid => name, ordered alphabetically by student name.
//  */
// function get_passenger_list() {
//     global $DB;
//     $students = $DB->get_records_sql(
//         "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
//          FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
//          LEFT JOIN {local_mxschool_permissions} p ON s.userid = p.userid
//          WHERE u.deleted = 0 AND (s.grade = 11 OR s.grade = 12) AND p.may_ride_with IS NOT NULL AND p.may_ride_with <> 'Over 21'
//          ORDER BY name"
//     );
//     return convert_records_to_list($students);
// }

// /**
//  * Queries the database to create a list of all eSignout driver records.
//  *
//  * @return array The drivers as esignoutid => name, ordered alphabetically by name.
//  */
// function get_all_driver_list() {
//     global $DB;
//     $drivers = $DB->get_records_sql(
//         "SELECT es.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename FROM {local_mxschool_esignout} es
//          LEFT JOIN {user} u ON es.userid = u.id LEFT JOIN {local_mxschool_permissions} p ON es.userid = p.userid
//          WHERE es.deleted = 0 AND u.deleted = 0 AND es.type = 'Driver' AND p.may_drive_passengers = 'Yes'
//          ORDER BY name ASC, es.time_modified DESC"
//     );
//     return convert_records_to_list($drivers);
// }

// /**
//  * Queries the database to create a list of currently available drivers.
//  * Drivers are defined as available if today is their departure day and they have not signed in.
//  *
//  * @param int $ignore The user id of a student to ignore (intended to be used to ignore the current student).
//  * @return array The drivers as esignoutid => name, ordered alphabetically by name.
//  */
// function get_current_driver_list($ignore = 0) {
//     global $DB;
//     $window = get_config('local_mxschool', 'esignout_trip_window');
//     $time = new DateTime('now', core_date::get_server_timezone_object());
//     $time->modify("-{$window} minutes");
//     $drivers = $DB->get_records_sql(
//         "SELECT es.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename FROM {local_mxschool_esignout} es
//          LEFT JOIN {user} u ON es.userid = u.id LEFT JOIN {local_mxschool_permissions} p ON es.userid = p.userid
//          WHERE es.deleted = 0 AND u.deleted = 0 AND es.type = 'Driver' AND es.time_created >= ? AND es.sign_in_time IS NULL
//          AND p.may_drive_passengers = 'Yes' AND u.id <> ? AND NOT EXISTS (
//              SELECT id FROM {local_mxschool_esignout} WHERE driverid = es.id AND userid = ?
//          ) ORDER BY name ASC, es.time_modified DESC", array($time->getTimestamp(), $ignore, $ignore)
//     );
//     return convert_records_to_list($drivers);
// }

/**
 * Queries the database to create a list of all the students who are required to fill out advisor selection form.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_student_with_advisor_form_enabled_list() {
    global $DB;
    $year = (int)format_date('Y') - 1;
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
    $year = (int)format_date('Y') - 1;
    $where = get_config('local_mxschool', 'advisor_form_enabled_who') === 'new' ? "s.admission_year = {$year}" : 's.grade <> 12';
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name, u.firstname, u.alternatename
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND NOT EXISTS (SELECT id FROM {local_mxschool_adv_selection} WHERE userid = s.userid) AND $where
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
         WHERE u.deleted = 0 AND s.grade <> 12 AND s.boarding_status_next_year = 'Boarder' AND NOT EXISTS (
             SELECT id FROM {local_mxschool_rooming} WHERE userid = s.userid
         ) ORDER BY name"
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
         WHERE u.deleted = 0 AND s.boarding_status = 'Boarder' AND NOT EXISTS (
             SELECT id FROM {local_mxschool_vt_trip} WHERE userid = s.userid
         ) ORDER BY name"
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

// /**
//  * Queries the database to create a list of all faculty who are able to approve eSignout.
//  *
//  * @return array The faculty who are able to approve eSignout as userid => name, ordered alphabetically by faculty name.
//  */
// function get_approver_list() {
//     global $DB;
//     $faculty = $DB->get_records_sql(
//         "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name FROM {local_mxschool_faculty} f
//          LEFT JOIN {user} u ON f.userid = u.id WHERE u.deleted = 0 and f.may_approve_signout = 1 ORDER BY name"
//     );
//     return convert_records_to_list($faculty);
// }

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
 * Creates a list of all possible start days for a weekend.
 *
 * @return array The possible start days for the weekend as offset => name in accending order.
 */
function get_weekend_start_day_list() {
    $days = array();
    $sunday = generate_datetime('Sunday this week');
    for ($i = -4; $i <= -1; $i++) {
        $day = clone $sunday;
        $day->modify("{$i} days");
        $days[$i] = $day->format('l');
    }
    return $days;
}

/**
 * Creates a list of all possible end days for a weekend.
 *
 * @return array The possible end days for the weekend as offset => name in accending order.
 */
function get_weekend_end_day_list() {
    $days = array();
    $sunday = generate_datetime('Sunday this week');
    for ($i = 0; $i <= 2; $i++) {
        $day = clone $sunday;
        $day->modify("{$i} days");
        $days[$i] = $day->format('l');
    }
    return $days;
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
            $time = generate_datetime($weekend->sunday_time);
            $time->modify("-1 day");
            $weekend->name = $time->format('m/d/y');
        }
    }
    return convert_records_to_list($weekends);
}

// /**
//  * Queries the database to create a list of all the dates for which there are eSignout records.
//  *
//  * @return array The dates for which there are eSignout records as timestamp => date (mm/dd/yy), in descending order by date.
//  */
// function get_esignout_date_list() {
//     global $DB;
//     $list = array();
//     $records = $DB->get_records_sql(
//         "SELECT es.id, es.departure_time FROM {local_mxschool_esignout} es LEFT JOIN {user} u ON es.userid = u.id
//          WHERE es.deleted = 0 AND u.deleted = 0 AND es.type <> 'Passenger' ORDER BY departure_time DESC"
//     );
//     if ($records) {
//         foreach ($records as $record) {
//             $date = new DateTime('now', core_date::get_server_timezone_object());
//             $date->setTimestamp($record->departure_time);
//             $date->modify('midnight');
//             if (!array_key_exists($date->getTimestamp(), $list)) {
//                 $list[$date->getTimestamp()] = $date->format('m/d/y');
//             }
//         }
//     }
//     return $list;
// }

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
    $date = generate_datetime($starttime);
    $date->modify('Sunday this week');
    while ($date->getTimestamp() < $endtime) {
        if (!isset($sorted[$date->getTimestamp()])) {
            $newweekend = new stdClass();
            $newweekend->sunday_time = $date->getTimestamp();
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
    $startdate = get_config('local_mxschool', $semester == 1 ? 'dorms_open_date' : 'second_semester_start_date');
    $enddate = get_config('local_mxschool', $semester == 1 ? 'second_semester_start_date' : 'dorms_close_date');
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

// /**
//  * Creates a list of the types of eSignout which a specified student has the permissions to perform.
//  *
//  * @param int $userid The user id of the student.
//  * @return array The types of eSignout which the student is allowed to perform.
//  */
// function get_esignout_type_list($userid = 0) {
//     global $DB;
//     $types = array('Driver', 'Passenger', 'Parent', 'Other');
//     $record = $DB->get_record_sql(
//         "SELECT p.may_drive_to_town AS maydrive, p.may_ride_with AS mayridewith, s.boarding_status AS boardingstatus
//          FROM {local_mxschool_student} s LEFT JOIN {local_mxschool_permissions} p ON p.userid = s.userid WHERE s.userid = ?",
//          array('userid' => $userid)
//     );
//     if ($record) {
//         if ($record->maydrive === 'No' || $record->boardingstatus !== 'Day') {
//             unset($types[array_search('Driver', $types)]);
//         }
//         if ($record->mayridewith === null || $record->mayridewith === 'Over 21') {
//             unset($types[array_search('Passenger', $types)]);
//         }
//         $types = array_values($types); // Reset the keys so that [0] can be the default option.
//     }
//     return $types;
// }

// /**
//  * Retrieves the destination and departure time fields from a esignout driver record.
//  *
//  * @param int $esignoutid The id of the driver's record.
//  * @return stdClass Object with properties destination, departurehour, departureminutes, and departureampm.
//  * @throws coding_exception If the esignout record is not a driver record.
//  */
// function get_driver_inheritable_fields($esignoutid) {
//     global $DB;
//     $record = $DB->get_record('local_mxschool_esignout', array('id' => $esignoutid));
//     if (!$record || $record->type !== 'Driver') {
//         throw new coding_exception('eSignout record is not a driver');
//     }
//     $result = new stdClass();
//     $result->destination = $record->destination;
//     $departuretime = new DateTime('now', core_date::get_server_timezone_object());
//     $departuretime->setTimestamp($record->departure_time);
//     $result->departurehour = $departuretime->format('g');
//     $minute = $departuretime->format('i');
//     $minute -= $minute % 15;
//     $result->departureminute = "{$minute}";
//     $result->departureampm = $departuretime->format('A') === 'PM';
//     return $result;
// }

// /**
//  * Signs in an eSignout record and records the timestamp.
//  *
//  * @param int $esignoutid The id of the record to sign in.
//  * @return string The text to display for the sign in time.
//  * @throws coding_exception If the esignout record does not exist or is already signed in.
//  */
// function sign_in_esignout($esignoutid) {
//     global $DB;
//     $record = $DB->get_record('local_mxschool_esignout', array('id' => $esignoutid));
//     if (!$record || $record->sign_in_time) {
//         throw new coding_exception('eSignout record doesn\'t exist or is already signed in');
//     }
//     $record->sign_in_time = time();
//     $DB->update_record('local_mxschool_esignout', $record);
//     return date('g:i A', $record->sign_in_time);
// }

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
 * Retrieves the default departure time for a vacation travel site.
 *
 * @param int $site The id of the site.
 * @return stdClass Object with properties year, month, day, hour, minute, ampm.
 */
function get_site_default_departure_time($site) {
    global $DB;
    $default = $DB->get_field_sql(
        "SELECT default_departure_time FROM {local_mxschool_vt_site} WHERE id = ? AND deleted = 0 AND enabled_departure = 1",
        array($site)
    );
    return enumerate_timestamp($default);
}

/**
 * Retrieves the default return time for a vacation travel site.
 *
 * @param int $site The id of the site.
 * @return stdClass Object with properties year, month, day, hour, minute, ampm.
 */
function get_site_default_return_time($site) {
    global $DB;
    $default = $DB->get_field_sql(
        "SELECT default_return_time FROM {local_mxschool_vt_site} WHERE id = ? AND deleted = 0 AND enabled_return = 1",
        array($site)
    );
    return enumerate_timestamp($default);
}
