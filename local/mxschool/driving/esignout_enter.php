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
 * Page for students to sign out for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once('esignout_form.php');
require_once(__DIR__.'/../classes/mx_notifications.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/events/page_visited.php');
require_once(__DIR__.'/../locallib.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_esignout', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('driving', 'local_mxschool') => '/local/mxschool/driving/index.php'
);
$redirect = new moodle_url($parents[array_keys($parents)[count($parents) - 1]]);
$url = '/local/mxschool/driving/esignout_enter.php';
$title = get_string('esignout', 'local_mxschool');
$queryfields = array('local_mxschool_esignout' => array('abbreviation' => 'es', 'fields' => array(
    'id', 'userid' => 'student', 'driverid' => 'driver', 'approverid' => 'approver', 'type' => 'type_select', 'passengers',
    'destination', 'departure_time' => 'departuretime', 'time_modified' => 'timemodified', 'time_created' => 'timecreated'
)));

$departuretime = new DateTime('now', core_date::get_server_timezone_object());
if ($id) {
    if (!$DB->record_exists('local_mxschool_esignout', array('id' => $id))) {
        redirect($redirect);
    }
    $data = get_record($queryfields, "es.id = ?", array($id));
    if ($isstudent) { // Students cannot edit existing esignout records beyond the edit window.
        $editwindow = new DateTime('now', core_date::get_server_timezone_object());
        $editwindow->setTimestamp($data->timecreated);
        $editwindow->modify('+30 minutes');
        $now = new DateTime('now', core_date::get_server_timezone_object());
        if ($now->getTimestamp() > $editwindow->getTimestamp() || $data->student != $USER->id) {
            redirect(new moodle_url($url));
        }
    }
    switch ($data->type_select) {
        case 'Driver':
            $data->passengers = json_decode($data->passengers);
            break;
        case 'Passenger':
            $driver = get_record($queryfields, "es.id = ?", array($data->driver));
            if (!isset($data->destination)) {
                $data->destination = $driver->destination;
            }
            if (!isset($data->departuretime)) {
                $data->departuretime = $driver->departuretime;
            }
            break;
        case 'Parent':
            break;
        default:
            $data->type_other = $data->type_select;
            $data->type_select = 'Other';
    }
    $departuretime->setTimestamp($data->departuretime);
} else {
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = time();
    if ($isstudent) {
        $data->student = $USER->id;
    }
}
$data->departuretime_hour = $departuretime->format('g');
$minute = $departuretime->format('i');
$data->departuretime_minute = $minute - $minute % 15;
$data->departuretime_ampm = $departuretime->format('A') === 'PM';
$departuretime->setTime(0, 0);
$data->date = $departuretime->getTimestamp();
$data->isstudent = $isstudent;
$students = get_student_list();
$userid = $isstudent ? $USER->id : (count($students) ? array_keys($students)[0] : 0);
if ($isstudent) {
    $record = $DB->get_record_sql(
        "SELECT CONCAT(u.firstname, ' ', u.lastname) AS student, p.may_drive_passengers AS maydrivepassengers
         FROM {user} u LEFT JOIN {local_mxschool_permissions} p ON u.id = p.userid WHERE u.id = ?", array($userid)
    );
    $data->maydrivepassengers = $record->maydrivepassengers === 'Yes' ? '1' : '0';
} else {
    $data->maydrivepassengers = '2'; // This is a work-around in order to have the value not be filled to '0' if validation fails.
}
$types = get_allowed_esignout_types_list($isstudent ? $USER->id : 0);
if (!isset($data->type_select)) {
    $data->type_select = $types[0];
}
$passengers = get_passengers_list($userid);
$drivers = array(0 => get_string('esignout_form_driver_default', 'local_mxschool')) + get_current_drivers_list($userid);
$approvers = array(0 => get_string('esignout_form_approver_default', 'local_mxschool')) + get_approver_list();

$event = \local_mxschool\event\page_visited::create(array('other' => array('page' => $title)));
$event->trigger();

$PAGE->set_url(new moodle_url($url));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('incourse');
foreach ($parents as $display => $url) {
    $PAGE->navbar->add($display, new moodle_url($url));
}
$PAGE->navbar->add($title);

$form = new esignout_form(null, array(
    'id' => $id, 'students' => $students, 'types' => $types, 'passengers' => $passengers, 'drivers' => $drivers,
    'approvers' => $approvers
));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->timemodified = time();
    switch($data->type_select) {
        case 'Passenger': // For a passenger record, the destination and departure fields are inherited.
            $data->destination = null;
            $data->departuretime = null;
            break;
        case 'Other':
            $data->type_select = $data->type_other;
        default: // Driver, Parent, and Other will all save their data on their own record.
            $data->driver = 0;
            $departuretime = new DateTime('now', core_date::get_server_timezone_object());
            $departuretime->setTimestamp($data->date);
            $departuretime->setTime(
                ($data->departuretime_hour % 12) + ($data->departuretime_ampm * 12), $data->departuretime_minute
            );
            $data->departuretime = $departuretime->getTimestamp();
    }
    $data->passengers = $data->type_select === 'Driver' ? json_encode(
        isset($data->passengers) ? $data->passengers : array()
    ) : null;
    $id = update_record($queryfields, $data);
    if ($data->type_select !== 'Passenger') { // For a driver, parent, or other record, the id and driverid should be the same.
        $data->id = $data->driver = $id;
        $id = update_record($queryfields, $data);
    }
    redirect(
        $form->get_redirect(), get_string('esignout_success', 'local_mxschool'), null, \core\output\notification::NOTIFY_SUCCESS
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form_page($form);

echo $output->header();
echo $output->heading($title.($isstudent ? " for {$record->student}" : ''));
echo $output->render($renderable);
echo $output->footer();
