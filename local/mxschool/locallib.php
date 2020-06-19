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
 * Local library functions for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__.'/podio/Podio.php');
require_once(__DIR__.'/podio/PodioItem.php');
/*
 * ========================
 * Page Setup Abstractions.
 * ========================
 */

/**
 * Sets the url, title, heading, and context of a page as well as adding a class to the body element for css.
 * Also logs that the page was visited.
 * Should be called to initialize all pages.
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
    $PAGE->add_body_class('mx-page');

    local_mxschool\event\page_viewed::create(array('other' => array('page' => $title)))->trigger();
}

/**
 * Sets the url, title, heading, context, layout, and navbar of a page as well as logging that the page was visited.
 * Should be called to initialize all pages except for index pages and edit pages.
 *
 * @param string $page The name of the page as referenced in the local_mxschool_subpackage table.
 * @param string $subpackage The subpackage to which the page belongs. A value of null indicates a package without subpackages.
 * @param string $package The package to which the subpackage belongs.
 * @throws coding_exception if the subpackage record does not exist or if the page cannot be found.
 */
function setup_mxschool_page($page, $subpackage, $package = 'mxschool') {
    global $DB, $PAGE;
    $record = $DB->get_record('local_mxschool_subpackage', array('package' => $package, 'subpackage' => $subpackage));
    if (!$record || !in_array($page, json_decode($record->pages))) {
        throw new coding_exception("page {$page} cannot be found in the subpackage with id {$record->id}");
    }

    $url = empty($subpackage) ? "/local/{$package}/{$page}.php" : "/local/{$package}/{$subpackage}/{$page}.php";
    $title = get_string(empty($subpackage) ? $page : "{$subpackage}:{$page}", "local_{$package}");

    setup_generic_page($url, $title);

    $PAGE->set_pagelayout('incourse');
    $PAGE->navbar->add(get_string('pluginname', 'local_mxschool'), new moodle_url('/local/mxschool'));
    if ($package !== 'mxschool') {
        $PAGE->navbar->add(get_string('pluginname', "local_{$package}"), new moodle_url("/local/{$package}"));
    }
    if (!empty($subpackage)) {
        $PAGE->navbar->add(get_string($subpackage, "local_{$package}"), new moodle_url("/local/{$package}/{$subpackage}"));
    }
    $PAGE->navbar->add($title, $url);
}

/**
 * Sets the url, title, heading, context, layout, and navbar of an edit page as well as logging that the page was visited.
 * Should be called to initialize all edit pages.
 *
 * @param string $page The name of the edit page. This function assumes that this is also the edit page's filename.
 * @param string $page The name of the edit page's parent page as referenced in the local_mxschool_subpackage table.
 * @param string $subpackage The subpackage to which the edit page and parent page both belong.
 *                           A value of null indicates a package without subpackages.
 * @param string $package The package to which the subpackage belongs.
 * @throws coding_exception if the subpackage record does not exist or if the parent page cannot be found.
 */
function setup_edit_page($page, $parent, $subpackage, $package = 'mxschool') {
    global $DB, $PAGE;
    $record = $DB->get_record('local_mxschool_subpackage', array('package' => $package, 'subpackage' => $subpackage));
    if (!$record || !in_array($parent, json_decode($record->pages))) {
        throw new coding_exception("parent page {$parent} cannot be found in the subpackage with id {$record->id}");
    }

    $url = empty($subpackage) ? "/local/{$package}/{$page}.php" : "/local/{$package}/{$subpackage}/{$page}.php";
    $title = get_string(empty($subpackage) ? $page : "{$subpackage}:{$page}", "local_{$package}");

    setup_generic_page($url, $title);

    $parenturl = "{$parent}.php";
    $parenttitle = get_string(empty($subpackage) ? $parent : "{$subpackage}:{$parent}", "local_{$package}");

    $PAGE->set_pagelayout('incourse');
    $PAGE->navbar->add(get_string('pluginname', 'local_mxschool'), new moodle_url('/local/mxschool'));
    if ($package !== 'mxschool') {
        $PAGE->navbar->add(get_string('pluginname', "local_{$package}"), new moodle_url("/local/{$package}"));
    }
    if (!empty($subpackage)) {
        $PAGE->navbar->add(get_string($subpackage, "local_{$package}"), new moodle_url("/local/{$package}/{$subpackage}"));
    }
    $PAGE->navbar->add($parenttitle, $parenturl);
    $PAGE->navbar->add($title, $url);
}

/**
 * Generates a renderable for the index with the specified id in the local_mxschool_subpackage table.
 *
 * @param int $id The id of the subpackage to be indexed.
 * @param bool $heading Whether the localized name of the subpackage should be included as the heading of the index.
 * @returnlocal_mxschool\output\index The specified index renderable.
 * @throws coding_exception if the subpackage record does not exist.
 */
function generate_index($id, $heading = false) {
    global $DB;
    $record = $DB->get_record('local_mxschool_subpackage', array('id' => $id));
    if (!$record) {
        throw new coding_exception("subpackage record with id {$id} does not exist");
    }
    $links = array();
    foreach (json_decode($record->pages) as $page) {
        if (empty($record->subpackage)) {
            $links[get_string($page, "local_{$record->package}")] = "/local/{$record->package}/{$page}.php";
        } else {
            $links[get_string("{$record->subpackage}:{$page}", "local_{$record->package}")]
                = "/local/{$record->package}/{$record->subpackage}/{$page}.php";
        }
    }
    $headingtext = empty($record->subpackage) ? get_string($record->package, "local_{$record->package}")
        : get_string($record->subpackage, "local_{$record->package}");
    return new local_mxschool\output\index($links, $heading ? $headingtext : false);
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

    $url = empty($subpackage) ? "/local/{$package}" : "/local/{$package}/{$subpackage}";
    $title = get_string(empty($subpackage) ? $package : $subpackage, "local_{$package}");

    setup_generic_page($url, $title);

    $output = $PAGE->get_renderer('local_mxschool');
    echo $output->header();
    echo $output->heading($title);
    echo $output->render(generate_index($id));
    echo $output->footer();
}

/**
 * Validates that the current user has admin access.
 * If the user does not have the site:config capability, they will be redirected to the dashboard.
 */
function redirect_non_admin() {
    if (!user_is_admin()) {
        redirect(new moodle_url('/'));
    }
}

/**
 * Redirects the user with a notification and logs the event of the redirect.
 *
 * @param moodle_url $url The url to redirect to.
 * @param string $notification The localized text to display on the notification.
 * @param string $action The type of action to be logged - either create, update, or delete.
 */
function logged_redirect($url, $notification, $action, $success = true) {
    global $PAGE;
    if ($success) {
        $params = array('other' => array('page' => $PAGE->title));
        switch($action) {
            case 'create':
                local_mxschool\event\record_created::create($params)->trigger();
                break;
            case 'update':
                local_mxschool\event\record_updated::create($params)->trigger();
                break;
            case 'delete':
                local_mxschool\event\record_deleted::create($params)->trigger();
                break;
            default:
                debugging("Invalid action type: {$action}", DEBUG_DEVELOPER);
        }
    }
    redirect(
        $url, $notification, null, $success ? core\output\notification::NOTIFY_SUCCESS : core\output\notification::NOTIFY_WARNING
    );
}

/**
 * Determines a fallback url for a form to redirect to when submitted or cancelled if when there is no referer.
 * Also intedned to be used when the current user can't currently access a form or the page is given invalid url parameters.
 *
 * @return moodle_url The fallback url for the form to redirect to.
 */
function get_fallback_url() {
    global $PAGE;
    return user_is_admin() ? $PAGE->navbar->children[count($PAGE->navbar->children) - 2]->action : new moodle_url('/');
}

/**
 * Redirects to the default fallback.
 * Intedned to be used when the current user can't currently access a form or the page is given invalid url parameters.
 */
function redirect_to_fallback() {
    redirect(get_fallback_url());
}

/*
 * ===================================
 * Database Manipulation Abstractions.
 * ===================================
 */

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
            $record->$header = $data->$name ?? null;
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
 * Sets the data for email prefence text fields for a particular email class.
 *
 * @param string $class The class identifier of the notification.
 * @param stdClass $data The data object.
 * @param string $prefix A prefix for the properties to be set.
 *                       - The properties set will be "{$prefix}_subject" and "{$prefix}_body['text']".
 */
function generate_email_preference_fields($class, &$data, $prefix = '') {
    global $DB;
    if (!$DB->record_exists('local_mxschool_notification', array('class' => $class))) {
        $default = array('class' => $class, 'body_html' => '');
        $DB->insert_record('local_mxschool_notification', (object) $default);
    }
    $notification = $DB->get_record('local_mxschool_notification', array('class' => $class));
    if ($prefix) {
        $data->{"{$prefix}_subject"} = $notification->subject;
        $data->{"{$prefix}_body"}['text'] = $notification->body_html;
    } else {
        $data->subject = $notification->subject;
        $data->body['text'] = $notification->body_html;
    }
}

/**
 * Updates a notification record in the database or inserts it if it doesn't already exist.
 *
 * @param string $class The class identifier of the notification.
 * @param stdClass $data The data object.
 * @param string $prefix A prefix for the properties to be set.
 *                       - The properties set will be "{$prefix}_subject" and "{$prefix}_body['text']".
 */
function update_notification($class, $data, $prefix = '') {
    global $DB;
    $record = array(
        'class' => $class,
        'subject' => $prefix ? $data->{"{$prefix}_subject"} : $data->subject,
        'body_html' => $prefix ? $data->{"{$prefix}_body"}['text'] : $data->body['text']
    );
    $id = $DB->get_field('local_mxschool_notification', 'id', array('class' => $class));
    if ($id) {
        $record['id'] = $id;
        $DB->update_record('local_mxschool_notification', (object) $record);
    } else {
        $DB->insert_record('local_mxschool_notification', (object) $record);
    }
}

/**
 * Deletes all student picture files.
 */
function clear_student_pictures() {
    $fs = get_file_storage();
    $files = $fs->get_area_files(1, 'local_mxschool', 'student_pictures', 0);
    foreach ($files as $file) {
        $file->delete();
    }
}

/*
 * ===============================================
 * DateTime Abstractions and Formatting Functions.
 * ===============================================
 */

/**
 * Generates a DateTime object from a time string or timestamp with the user's timezone.
 *
 * @param string|int $time A date/time string in any of the supported formats (https://www.php.net/manual/en/datetime.formats.php)
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
 * @param string|int $time A date/time string in any of the supported formats (https://www.php.net/manual/en/datetime.formats.php)
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
 * @param int $step The resolution of the time in minutes.
 */
function generate_time_selector_fields(&$data, $prefix, $step = 1) {
    $time = generate_datetime($data->{"{$prefix}_date"});
    $data->{"{$prefix}_time_hour"} = $time->format('g');
    $data->{"{$prefix}_time_minute"} = (string) ((int) ($time->format('i') / $step) * $step);
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
    $data = (object) $data;
    $time = generate_datetime($data->{"{$prefix}_date"});
    $time->setTime($data->{"{$prefix}_time_hour"} % 12 + $data->{"{$prefix}_time_ampm"} * 12, $data->{"{$prefix}_time_minute"});
    return $time->getTimestamp();
}

/**
 * Helper method to convert a timestamp into an object.
 *
 * @param int $timestamp The timestamp to convert.
 * @param int $step The resolution of the time in minutes.
 * @return stdClass Object with properties year, month, day, hour, minute, ampm.
 */
function enumerate_timestamp($timestamp, $step = 1) {
    if ($timestamp) {
        $time = generate_datetime($timestamp);
        return (object) array(
            'year' => $time->format('Y'),
            'month' => $time->format('n'),
            'day' => $time->format('j'),
            'hour' => $time->format('g'),
            'minute' => (string) ((int) ($time->format('i') / $step) * $step),
            'ampm' => $time->format('A')
        );
    }
    return (object) array('year' => '', 'month' => '', 'day' => '', 'hour' => '', 'minute' => '', 'ampm' => '');
}

/**
 * Converts a boolean value to a 'yes' or 'no' language string.
 *
 * @param bool $boolean The boolean value.
 * @return string The language appropriate 'yes' or 'no' value.
 */
function format_boolean($boolean) {
    return $boolean ? get_string('yes') : get_string('no');
}

/**
 * Formats a student's name to "Last, First (Preferred)" or "Last, First" using the data in their user record.
 * Also checks whether the user record has been deleted.
 *
 * @param int $userid The userid of the student.
 * @return string The formatted name, an empty string if the user record has been deleted.
 * @throws coding_exception If the specified user record cannot be found.
 */
function format_student_name($userid) {
    global $DB;
    $record = $DB->get_record('user', array('id' => $userid));
    if (!$record) {
        throw new coding_exception("student user record with id {$userid} could not be found");
    }
    if ($record->deleted) {
        return '';
    }
    $alternate = empty($record->alternatename) ? '' : $record->alternatename;
    return "{$record->lastname}, {$record->firstname}" . ($alternate && $alternate !== $record->firstname ? " ({$alternate})" : '');
}

/**
 * Formats a faculty's name to "Last, First" or "First Last" using the data in their user record.
 * Also checks whether the user record has been deleted.
 *
 * @param int $userid The userid of the faculty.
 * @param bool $inverted Whether to format the name as "Last, First" or "First Last"
 * @return string The formatted name, an empty string if the user record has been deleted.
 * @throws coding_exception If the specified user record cannot be found.
 */
function format_faculty_name($userid, $inverted = true) {
    global $DB;
    $record = $DB->get_record('user', array('id' => $userid));
    if (!$record) {
        throw new coding_exception("faculty user record with id {$userid} could not be found");
    }
    if ($record->deleted) {
        return '';
    }
    return $inverted ? "{$record->lastname}, {$record->firstname}" : "{$record->firstname} {$record->lastname}";
}

/**
 * Formats a dorm's name using the data in its dorm record.
 * Also checks whether the dorm record has been deleted.
 *
 * @param int $id The id of the dorm.
 * @return string The formatted name, an empty string if the dorm record has been deleted.
 * @throws coding_exception If the specified dorm record cannot be found.
 */
function format_dorm_name($id) {
    global $DB;
    $record = $DB->get_record('local_mxschool_dorm', array('id' => $id));
    if (!$record) {
        throw new coding_exception("dorm record with id {$id} could not be found");
    }
    if ($record->deleted) {
        return '';
    }
    return $record->name;
}

/**
 * Converts an array of objects with properties id and value to an array of form id => value.
 *
 * @param array $records The record objects to convert.
 * @return array The same data in the form id => value.
 */
function convert_records_to_list($records) {
    $list = array();
    if (is_array($records)) { // Could have a value of false if the original query yielded no results.
        foreach ($records as $record) {
            $list[$record->id] = $record->value;
        }
    }
    return $list;
}

/**
 * Converts an array of objects with property id to an array of form id => formatted_name.
 * Uses the student name format: "Last, First (Preferred)" or "Last, First"
 *
 * @param array $records The student record objects to convert.
 * @return array The same data in the form id => formatted_name.
 * @throws coding_exception If any of the specified user records cannot be found.
 */
function convert_student_records_to_list($records) {
    $list = array();
    if (is_array($records)) { // Could have a value of false if the original query yielded no results.
        foreach ($records as $record) {
            $list[$record->id] = format_student_name($record->id);
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

/*
 * =================================
 * Permissions Validation Functions.
 * =================================
 */

/**
 * Determines whether the current user has admin capabilities.
 * This function is probably unncessary, but it does make the code more compact and readable in many places.
 *
 * @return bool Whether the user has admin capabilities.
 */
function user_is_admin() {
    return has_capability('moodle/site:config', context_system::instance());
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
 * Determines whether a specified user is a student who is permitted to access the advisor selectino form.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access the advisor selection form.
 */
function student_may_access_advisor_selection($userid) {
    $start = (int) (get_config('local_mxschool', 'advisor_form_start_date') ?: get_config('local_mxschool', 'dorms_open_date'));
    $stop = (int) (get_config('local_mxschool', 'advisor_form_stop_date') ?: get_config('local_mxschool', 'dorms_close_date'));
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
    $start = (int) (get_config('local_mxschool', 'rooming_form_start_date') ?: get_config('local_mxschool', 'dorms_open_date'));
    $stop = (int) (get_config('local_mxschool', 'rooming_form_stop_date') ?: get_config('local_mxschool', 'dorms_close_date'));
    return $start && $stop && time() > $start && time() < $stop && array_key_exists($userid, get_boarding_next_year_student_list());
}

/**
 * Determines whether a specified user is a student who is permitted to access the vacation travel form.
 *
 * @param int $id The user id of the student to check.
 * @return bool Whether the specified student is permitted to access the vacation travel form.
 */
function student_may_access_vacation_travel($userid) {
    $start = (int) (get_config('local_mxschool', 'vacation_form_start_date') ?: get_config('local_mxschool', 'dorms_open_date'));
    $stop = (int) (get_config('local_mxschool', 'vacation_form_stop_date') ?: get_config('local_mxschool', 'dorms_close_date'));
    return $start && $stop && time() > $start && time() < $stop && array_key_exists($userid, get_boarding_student_list());
}

/*
 * ====================================
 * URL Parameter Querying Abstractions.
 * ====================================
 */

/**
 * Determines the dorm id to display for a faculty.
 *
 * The priorities of this function are as follows:
 * 1) An id specified as a 'dorm' GET parameter, if the id is valid.
 * 2) The dorm of the currently logged in faculty member, if it exists.
 * 3) An empty string.
 *
 * NOTE: The $_GET superglobal is used in this function in order to differentiate between an unset parameter and the all option.
 *       Its value is only used after being checked as numeric or empty to avoid potential security issues.
 *
 * @param bool $includeday Whether to include day houses or limit to boading houses.
 * @return string The dorm id or an empty string.
 */
function get_param_faculty_dorm($includeday = true) {
    global $DB, $USER;
    if (isset($_GET['dorm']) && (is_numeric($_GET['dorm']) || empty($_GET['dorm']))) {
        $dorm = $_GET['dorm']; // The value is now safe to use.
        // An empty parameter indicates that search has taken place with the all option selected.
        // A value o f-2 indicates all boarding houses; a value of -1 indicates all day houses.
        if (empty($dorm) || isset(get_dorm_list($includeday)[$dorm]) || ($includeday && in_array($dorm, array(-1, -2)))) {
            return $dorm;
        }
    }
    return $DB->get_field('local_mxschool_faculty', 'dormid', array('userid' => $USER->id)) ?: '';
}

/**
 * Determines the date to be selected.
 *
 * The priorities of this function are as follows:
 * 1) An id specified as a 'date' GET parameter.
 * 2) The current date.
 *
 * NOTE: The $_GET superglobal is used in this function in order to differentiate between an unset parameter and the all option.
 *       Its value is only used after being checked as numeric or empty to avoid potential security issues.
 *
 * @return string The timestamp of the midnight on the desired date.
 */
function get_param_current_date() {
    return isset($_GET['date']) && (is_numeric($_GET['date']) || empty($_GET['date']))
        ? $_GET['date'] : generate_datetime('midnight')->getTimestamp();
}

/**
 * Determines the weekend id to be selected.
 *
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
    $weekend = optional_param('weekend', 0, PARAM_INT);
    if ($weekend && $DB->record_exists('local_mxschool_weekend', array('id' => $weekend))) {
        return $weekend;
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
        "SELECT id
         FROM {local_mxschool_weekend}
         WHERE sunday_time >= ? AND sunday_time >= ? AND sunday_time < ?
         ORDER BY sunday_time", array($timestamp, $starttime, $endtime), IGNORE_MULTIPLE
    );
    if ($weekend) {
        return $weekend;
    }
    $weekend = $DB->get_field_sql(
        "SELECT id
         FROM {local_mxschool_weekend}
         WHERE sunday_time >= ? AND sunday_time < ?
         ORDER BY sunday_time DESC", array($starttime, $endtime), IGNORE_MULTIPLE
    );
    if ($weekend) {
        return $weekend;
    }
    throw new moodle_exception('there are no valid weekend records in the database');
}

/**
 * Determines the semester ('1' or '2') to be selected.
 *
 * The priorities of this function are as follows:
 * 1) A value specified as a 'semester' GET parameter.
 * 2) The current semster.
 * 3) The first semester if before the dorms open date; the second semester if after the dorms close date.
 *
 * @return string The semester, as specified.
 */
function get_param_current_semester() {
    return isset($_GET['semester']) && in_array($_GET['semester'], array(1, 2)) ? $_GET['semester'] : get_current_semester();
}

/**
 * Determines the current semester ('1' or '2').
 * This is determined to be the '1' if the current date is before the start date of the second semester and '2' if it is after.
 *
 * @return string The semester, as specified.
 */
function get_current_semester() {
    return generate_datetime()->getTimestamp() < get_config('local_mxschool', 'second_semester_start_date') ? '1' : '2';
}

/*
 * ==========================================
 * Database Query for Record List Functions.
 * ==========================================
 */

/**
 * Queries the database to create a list of all the students.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_student_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0
         ORDER BY name"
    );
    return convert_student_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who are boarders.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_boarding_student_list() {
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
 * Queries the database to create a list of all the students who will be boarders next year.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_boarding_next_year_student_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND s.grade <> 12 AND s.boarding_status_next_year = 'Boarder'
         ORDER BY name"
    );
    return convert_student_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who are in a specified dorm.
 *
 * @param int $dorm the id of the desired dorm.
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_student_in_dorm_list($dorm) {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND s.dormid = ?
         ORDER BY name", array($dorm)
    );
    return convert_student_records_to_list($students);
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
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
                                         LEFT JOIN {local_mxschool_permissions} p ON s.userid = p.userid
         WHERE u.deleted = 0 AND p.license_date IS NOT NULL
         ORDER BY name"
    );
    return convert_student_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who are required to fill out advisor selection form.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_student_with_advisor_form_enabled_list() {
    global $DB;
    $year = (int)format_date('Y') - 1;
    $where = get_config('local_mxschool', 'advisor_form_enabled_who') === 'new' ? "s.admission_year = {$year}" : 's.grade <> 12';
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND {$where}
         ORDER BY name"
    );
    return convert_student_records_to_list($students);
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
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND NOT EXISTS (SELECT id FROM {local_mxschool_adv_selection} WHERE userid = s.userid) AND {$where}
         ORDER BY name"
     );
    return convert_student_records_to_list($students);
}

/**
 * Queries the database to create a list of all the new student - advisor pairs.
 * Any student who selected to changed advisors on their advisor selection form will be included.
 *
 * @return array The students and advisors as studentuserid => advisoruserid.
 */
function get_new_student_advisor_pair_list() {
    global $DB;
    $records = $DB->get_records_sql(
        "SELECT u.id, asf.selectedid AS value
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
                                         LEFT JOIN {local_mxschool_adv_selection} asf ON s.userid = asf.userid
         WHERE u.deleted = 0 AND asf.keep_current = 0 AND asf.selectedid <> 0"
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
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND s.grade <> 12 AND s.boarding_status_next_year = 'Boarder'
                             AND NOT EXISTS (SELECT id FROM {local_mxschool_rooming} WHERE userid = s.userid)
         ORDER BY name"
    );
    return convert_student_records_to_list($students);
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
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
                                         LEFT JOIN {local_mxschool_student} ss ON ss.userid = ?
         WHERE u.deleted = 0 AND s.grade <> 12 AND s.boarding_status_next_year = 'Boarder'
                             AND s.gender = ss.gender AND s.userid <> ss.userid
         ORDER BY name", array($userid)
    );
    return convert_student_records_to_list($students);
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
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
                                         LEFT JOIN {local_mxschool_student} ss ON ss.userid = ?
         WHERE u.deleted = 0 AND s.grade <> 12 AND s.boarding_status_next_year = 'Boarder'
                             AND s.grade = ss.grade AND s.gender = ss.gender AND s.userid <> ss.userid
         ORDER BY name", array($userid)
    );
    return convert_student_records_to_list($students);
}

/**
 * Queries the database to create a list of all the students who are boarders and have not filled out a vacation travel form.
 *
 * @return array The students as userid => name, ordered alphabetically by student name.
 */
function get_student_without_vacation_travel_form_list() {
    global $DB;
    $students = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS name
         FROM {local_mxschool_student} s LEFT JOIN {user} u ON s.userid = u.id
         WHERE u.deleted = 0 AND s.boarding_status = 'Boarder'
                             AND NOT EXISTS (SELECT id FROM {local_mxschool_vt_trip} WHERE userid = s.userid)
         ORDER BY name"
    );
    return convert_student_records_to_list($students);
}

/**
 * Queries the database to create a list of all the faculty.
 *
 * @return array The faculty as userid => name, ordered alphabetically by faculty name.
 */
function get_faculty_list() {
    global $DB;
    $faculty = $DB->get_records_sql(
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS value
         FROM {local_mxschool_faculty} f LEFT JOIN {user} u ON f.userid = u.id
         WHERE u.deleted = 0
         ORDER BY value"
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
        "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS value
         FROM {local_mxschool_faculty} f LEFT JOIN {user} u ON f.userid = u.id
         WHERE u.deleted = 0 AND f.advisory_available = 1 AND f.advisory_closing = 0
         ORDER BY value"
    );
    return convert_records_to_list($advisors);
}

/**
 * Queries the database to create a list of all the available dorms.
 *
 * @param bool $includeday Whether to include day houses or limit to boading houses.
 * @return array The available dorms as id => name, ordered alphabetically by dorm name.
 */
function get_dorm_list($includeday = true) {
    global $DB;
    $where = $includeday ? '' : "AND type = 'Boarding'";
    $dorms = $DB->get_records_select(
        'local_mxschool_dorm', "deleted = 0 AND available = 1 {$where}", null, 'value', 'id, name AS value'
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
    $weekends = $DB->get_records_select(
        'local_mxschool_weekend', 'sunday_time >= ? AND sunday_time < ?',
        array(get_config('local_mxschool', 'dorms_open_date'), get_config('local_mxschool', 'dorms_close_date')), 'sunday',
        'id, sunday_time AS sunday'
    );
    if ($weekends) {
        foreach ($weekends as $weekend) {
            $time = generate_datetime($weekend->sunday);
            $time->modify("-1 day");
            $weekend->value = $time->format('m/d/y');
        }
    }
    return convert_records_to_list($weekends);
}

/**
 * Queries the database to create a list of all the vacation travel departure sites of a particular type.
 *
 * @param string|null $type The type to filter by, if no type is provided all will be returned.
 * @return array The vacation travel departure sites as id => name, ordered alphabetically by site name.
 */
function get_vacation_travel_departure_sites_list($type = null) {
    global $DB;
    $where = $type ? "AND type = '{$type}'" : '';
    $sites = $DB->get_records_select(
        'local_mxschool_vt_site', "deleted = 0 AND enabled_departure = 1 {$where}", null, 'value', 'id, name AS value'
    );
    $list = convert_records_to_list($sites);
    if (!$type || $type === 'Plane' || $type === 'Train' || $type === 'Bus') {
        $list += array(0 => get_string('vacation_travel:form:departure:dep_site:other', 'local_mxschool'));
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
    $where = $type ? "AND type = '{$type}'" : '';
    $sites = $DB->get_records_select(
        'local_mxschool_vt_site', "deleted = 0 AND enabled_return = 1 {$where}", null, 'value', 'id, name AS value'
    );
    $list = convert_records_to_list($sites);
    if (!$type || $type === 'Plane' || $type === 'Train' || $type === 'Bus') {
        $list += array(0 => get_string('vacation_travel:form:return:ret_site:other', 'local_mxschool'));
    }
    return $list;
}

/*
 * ============================================
 * Miscellaneous Subpackage-Specific Functions.
 * ============================================
 */

/* Check-In Sheets and Weekend Forms. */

/**
 * Creates a list of all the weekend types.
 *
 * @return array The weekend types as internal_name => localized_name, in order by type.
 */
function get_weekend_type_list() {
    return array(
        'Open' => get_string('weekend_type:open', 'local_mxschool'),
        'Closed' => get_string('weekend_type:closed', 'local_mxschool'),
        'Free' => get_string('weekend_type:free', 'local_mxschool'),
        'Vacation' => get_string('weekend_type:vacation', 'local_mxschool'),
    );
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
 * Adds default weekend records for all Sundays between two timestamps.
 *
 * @param int $starttime The timestamp for the beginning of the range.
 * @param int $endtime The timestamp for the end of the range.
 * @return array The fully populated list of weekend records occuring between the two timestamps, ordered by date.
 */
function generate_weekend_records($starttime, $endtime) {
    global $DB;
    $weekends = $DB->get_records_select(
        'local_mxschool_weekend', 'sunday_time >= ? AND sunday_time < ?', array($starttime, $endtime), '', 'sunday_time AS sunday'
    );
    $sorted = array();
    foreach ($weekends as $weekend) {
        $sorted[$weekend->sunday] = $weekend;
    }
    $date = generate_datetime($starttime);
    $date->modify('Sunday this week');
    while ($date->getTimestamp() < $endtime) {
        if (empty($sorted[$date->getTimestamp()])) {
            $DB->insert_record('local_mxschool_weekend', (object) array('sunday_time' => $date->getTimestamp()));
        }
        $date->modify('+1 week');
    }
    return $DB->get_records_select(
        'local_mxschool_weekend', 'sunday_time >= ? AND sunday_time < ?', array($starttime, $endtime), 'sunday_time'
    );
}

/**
 * Queries the database to determine whether a date occurs within a valid weekend.
 *
 * @param string|int $date A date/time string in a format accepted by date() (https://www.php.net/manual/en/function.date.php)
 *                         or a timestamp.
 * @return bool Whther the timestamp is between the start and end times of a weekend in the database.
 */
function date_is_in_weekend($date = 'now') {
    global $DB;
    $timestamp = generate_datetime($date)->getTimestamp();
    $starttime = get_config('local_mxschool', 'dorms_open_date');
    $endtime = get_config('local_mxschool', 'dorms_close_date');
    if ($timestamp < $starttime || $timestamp >= $endtime) { // No need to query if we are outside the range of weekends.
        return false;
    }
    $weekends = $DB->get_records_select(
        'local_mxschool_weekend', 'sunday_time >= ? AND sunday_time < ?', array($starttime, $endtime)
    );
    if ($weekends) {
        foreach ($weekends as $weekend) {
            $start = generate_datetime($weekend->sunday_time);
            $start->modify("{$weekend->start_offset} days");
            $endoffset = $weekend->end_offset + 1; // Add an additional day to get to the end of the weekend.
            $end = generate_datetime($weekend->sunday_time);
            $end->modify("{$endoffset} days");
            if ($timestamp >= $start->getTimestamp() && $timestamp < $end->getTimestamp()) {
                return true;
            }
        }
    }
    return false;
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
        "SELECT COUNT(wf.id)
         FROM {local_mxschool_student} s LEFT JOIN {local_mxschool_weekend_form} wf ON s.userid = wf.userid
                                         LEFT JOIN {local_mxschool_weekend} w ON wf.weekendid = w.id
         WHERE s.userid = ? AND sunday_time >= ? AND sunday_time < ? AND wf.active = 1 AND (w.type = 'open' OR w.type = 'closed')",
        array($userid, $startdate, $enddate)
    );
}

/* Rooming. */

/**
 * Creates a list of all the room types for a particular gender.
 *
 * @param string $gender The gender to check for - either 'M', 'F', or ''.
 * @return array The room types as internal_name => localized_name, in order by type.
 */
function get_room_type_list($gender = '') {
    $roomtypes = array(
        'Single' => get_string('room_type:single', 'local_mxschool'),
        'Double' => get_string('room_type:double', 'local_mxschool')
    );
    if ($gender !== 'M') {
        $roomtypes['Quad'] = get_string('room_type:quad', 'local_mxschool');
    }
    return $roomtypes;
}

/* Vacation Travel. */

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

/* Health Pass. @author Cannon Caspar, class of 2021 <cpcaspar@mxschool.edu> */

/**
* Returns a list of all users
*
* @return array The users as userid => name, ordered alphabetically
*/
function get_user_list() {
	global $DB;
	$users = $DB->get_records_sql(
	    "SELECT u.id, CONCAT(u.lastname, ', ', u.firstname) AS value
	     FROM {user} u
	     WHERE u.deleted = 0
	     ORDER BY value"
	);
	return convert_records_to_list($users);
}

/**
* Returns a list of all dates where a healthform has been submitted
*
* @return array The times as formatted timestamp => formatted date
*/
function get_healthform_dates() {
	global $DB;
	$list = array();
	$dates = $DB->get_records_sql(
		"SELECT hp.id, hp.form_submitted
		FROM {local_mxschool_healthpass} hp
		ORDER BY form_submitted DESC"
	);
	if ($dates) {
         foreach ($dates as $record) {
             $date = generate_datetime($record->form_submitted);
             $date->modify('midnight');
             if (!array_key_exists($date->getTimestamp(), $list)) {
                 $list[$date->getTimestamp()] = $date->format('m/d/y');
             }
         }
     }
	return $list;
}

/**
* Given the Health Form data, passes the information to Podio
*
* @param stdClass data, the form data
* @return String response. Whether or not the student was approved or denied
*/
 function podio_submit($data) {
	 // TODO: Make these variables configurable
	 $client_id = get_config('local_mxschool', 'client_id');
	 $client_secret = get_config('local_mxschool', 'client_secret');
	 $app_id = get_config('local_mxschool', 'app_id');
	 $app_token = get_config('local_mxschool', 'app_token');
	 $url = get_config('local_mxschool', 'podio_url');
	 $contact_name = 1421936959; // not sure how to get this number

	 // On Podio, YES is 1, NO is 2
	 $attributes = array(
		 'fields' => array(
			 'contact-name' => $contact_name,
			 'review-date' => generate_datetime($data->timecreated)->format('Y-m-d h:i:s'),
			 'enter-temperature' => $data->body_temperature,
			 'day-student-is-anyone-in-your-home-positive-for-or-susp' => $data->anyone_sick_at_home==0 ? 2 : 1,
			 'do-you-have-a-fever-or-feel-feverish' => $data->has_fever==0 ? 2 : 1,
			 'do-you-have-a-sore-throat' => $data->has_sore_throat==0 ? 2 : 1,
			 'do-you-have-a-cough' => $data->has_cough==0 ? 2 : 1,
			 'do-you-have-nasal-congestion-or-runny-nose-not-related-' => $data->has_runny_nose==0 ? 2 : 1,
			 'do-you-have-muscle-aches' => $data->has_muscle_aches==0 ? 2 : 1,
			 'do-you-have-a-loss-of-smell-or-taste' => $data->has_loss_of_sense==0 ? 2 : 1,
			 'do-you-have-shortness-of-breath' => $data->has_short_breath==0 ? 2 : 1
	 	)
	 );

	 $options = array(
		 'file_download' => 1,
		 'oauth_request' => 1
	 );
	 // post to the form
	 Podio::setup($client_id, $client_secret);
	 Podio::authenticate_with_app($app_id, $app_token);
	 $item = PodioItem::create($app_id, $attributes, $options);
	 // get response
	 $reponse = PodioItem::get($item->item_id);
	 return $reponse->fields->offsetGet('status')->values[0]['value'];
 }

 /**
 * Given a user's $id, gets the user's healthform data from today
 *
 * @param int id, the user's id
 * @return stdClass $info. $info->submitted_today is true if the user submitted today.
 *					  $info->status is "Approved" if approved and "Denied" if the form was denied today
 */
 function get_todays_healthform_info($id) {
	 global $DB;
	 $today = generate_datetime(time());
	 $today->modify('midnight');
	 $healthforms = $DB->get_records_sql(
		 "SELECT hp.id, hp.userid, hp.status
		 FROM {local_mxschool_healthpass} hp
		 WHERE hp.form_submitted >= {$today->getTimestamp()}"
	 );
	 $info = new stdClass();
	 foreach($healthforms as $form) {
		 if($form->userid == $id) {
			 $info->submitted_today = true;
			 $info->status = $form->status;
			 return $info;
	      }
	 }
	 $info->submitted_today = false;
	 $info->status = 'Unsubmitted';
	 return $info;
 }
