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
 * Page for students to sign out for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage esignout
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/notification/esignout.php');
require_once('esignout_form.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_esignout', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('esignout', 'local_mxschool') => '/local/mxschool/esignout/index.php'
);
$redirect = get_redirect($parents);
$url = '/local/mxschool/esignout/esignout_enter.php';
$title = get_string('esignout', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$queryfields = array('local_mxschool_esignout' => array('abbreviation' => 'es', 'fields' => array(
    'id', 'userid' => 'student', 'driverid' => 'driver', 'approverid' => 'approver', 'type' => 'type_select', 'passengers',
    'destination', 'departure_time' => 'departure_date', 'time_created' => 'timecreated', 'time_modified' => 'timemodified'
)));

if ($isstudent && !student_may_access_esignout($USER->id)) {
    redirect($redirect);
}
if ($id) {
    if (!$DB->record_exists('local_mxschool_esignout', array('id' => $id))) {
        redirect($redirect);
    }
    $data = get_record($queryfields, "es.id = ?", array($id));
    if ($isstudent) { // Students cannot edit existing esignout records beyond the edit window.
        $editwindow = get_config('local_mxschool', 'esignout_edit_window');
        $editcutoff = new DateTime('now', core_date::get_server_timezone_object());
        $editcutoff->setTimestamp($data->timecreated);
        $editcutoff->modify("+{$editwindow} minutes");
        $now = new DateTime('now', core_date::get_server_timezone_object());
        if ($now->getTimestamp() > $editcutoff->getTimestamp() || $data->student !== $USER->id) {
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
            if (!isset($data->departure_date)) {
                $data->departure_date = $driver->departure_date;
            }
            break;
        case 'Parent':
            break;
        default:
            $data->type_other = $data->type_select;
            $data->type_select = 'Other';
    }
} else {
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = time();
    $data->departure_date = time();
    if ($isstudent) {
        $data->student = $USER->id;
    }
}
if ($isstudent) {
    $record = $DB->get_record_sql(
        "SELECT CONCAT(u.lastname, ', ', u.firstname) AS student, u.firstname, u.alternatename FROM {user} u WHERE u.id = ?",
        array($USER->id)
    );
    $record->student = $record->student . (
        $record->alternatename && $record->alternatename !== $record->firstname ? " ({$record->alternatename})" : ''
    );
}
$data->isstudent = $isstudent ? '1' : '0';
$data->instructions = get_config('local_mxschool', 'esignout_form_instructions');
$data->passengerswarning = get_config('local_mxschool', 'esignout_form_warning_nopassengers');
generate_time_selector_fields($data, 'departure', 15);
$data->parentwarning = get_config('local_mxschool', 'esignout_form_warning_needparent');
$data->specificwarning = get_config('local_mxschool', 'esignout_form_warning_onlyspecific');
$students = get_esignout_student_list();
$types = get_esignout_type_list();
$passengers = get_passenger_list();
$drivers = array(0 => get_string('form_select_default', 'local_mxschool')) + get_all_driver_list();
$approvers = array(0 => get_string('form_select_default', 'local_mxschool')) + get_approver_list();

$form = new esignout_form(array(
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
            $data->date = null;
            break;
        case 'Other':
            $data->type_select = $data->type_other;
        default: // Driver, Parent, and Other will all save their data on their own record.
            $data->driver = 0;
            $data->departure_date = generate_timestamp($data, 'departure');
    }
    $data->passengers = $data->type_select === 'Driver' ? json_encode($data->passengers ?? array()) : null;
    $id = update_record($queryfields, $data);
    if ($data->type_select !== 'Passenger') { // For a driver, parent, or other record, the id and driverid should be the same.
        $record = $DB->get_record('local_mxschool_esignout', array('id' => $id));
        $record->driverid = $id;
        $DB->update_record('local_mxschool_esignout', $record);
    }
    $result = (new \local_mxschool\local\esignout\submitted($id))->send();
    logged_redirect(
        $form->get_redirect(), get_string('esignout_success', 'local_mxschool'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$bottominstructions = get_config('local_mxschool', 'esignout_form_instructions_bottom');
$bottominstructions = str_replace(
    '{minutes}', get_config('local_mxschool', 'esignout_edit_window'), $bottominstructions
);
$formrenderable = new \local_mxschool\output\form($form, false, $bottominstructions);
$jsrenderable = new \local_mxschool\output\amd_module('local_mxschool/esignout_form');

echo $output->header();
if (
    !$isstudent || !get_config('local_mxschool', 'esignout_form_ipenabled')
    || $_SERVER['REMOTE_ADDR'] === get_config('local_mxschool', 'school_ip')
) {
    echo $output->heading($title . ($isstudent ? " for {$record->student}" : ''));
    echo $output->render($formrenderable);
    echo $output->render($jsrenderable);
} else {
    echo $output->heading(get_config('local_mxschool', 'esignout_form_iperror'));
}
echo $output->footer();
