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
 * Page for students to sign out to an off-campus location for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  off_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/off_campus_form.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/signout:manage_off_campus', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('form', 'off_campus', 'signout');

$queryfields = array('local_signout_off_campus' => array('abbreviation' => 'oc', 'fields' => array(
    'id', 'userid' => 'student', 'driverid' => 'driver', 'approverid' => 'approver', 'type' => 'type_select', 'passengers',
    'destination', 'departure_time' => 'departure_date', 'time_created' => 'timecreated', 'time_modified' => 'timemodified'
)));

if ($isstudent && !student_may_access_off_campus_signout($USER->id)) {
    redirect_to_fallback();
}
if ($id) { // Updating an existing record.
    if (!$DB->record_exists('local_signout_off_campus', array('id' => $id))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, "oc.id = ?", array($id));
    if ($isstudent) { // Students cannot edit existing off-campus signout records beyond the edit window.
        if (generate_datetime()->getTimestamp() > get_edit_cutoff($data->timecreated) || $data->student !== $USER->id) {
            redirect_to_fallback();
        }
    }
    switch ($data->type_select) {
        case 'Driver':
            $data->passengers = json_decode($data->passengers);
            break;
        case 'Passenger':
            $driver = get_record($queryfields, "oc.id = ?", array($data->driver));
            $data->destination = $driver->destination;
            $data->departure_date = $driver->departure_date;
            break;
        case 'Parent':
        case 'Rideshare':
            break;
        default:
            $data->type_other = $data->type_select;
            $data->type_select = 'Other';
    }
} else { // Creating a new record.
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = $data->departure_date = time();
    if ($isstudent) {
        $data->student = $USER->id;
    }
}
$data->isstudent = $isstudent ? '1' : '0';
$data->instructions = get_config('local_signout', 'off_campus_form_instructions_passenger');
$data->passengerswarning = get_config('local_signout', 'off_campus_form_warning_nopassengers');
generate_time_selector_fields($data, 'departure', 15);
$data->parentwarning = get_config('local_signout', 'off_campus_form_warning_needparent');
$data->specificwarning = get_config('local_signout', 'off_campus_form_warning_onlyspecific');
$students = get_student_list();
$types = get_off_campus_type_list();
$passengers = get_permitted_passenger_list();
$drivers = array(0 => get_string('form_select_default', 'local_mxschool')) + get_permitted_driver_list();
$approvers = array(0 => get_string('form_select_default', 'local_mxschool')) + get_approver_list();

$form = new off_campus_form(array(
    'students' => $students, 'types' => $types, 'passengers' => $passengers, 'drivers' => $drivers, 'approvers' => $approvers
));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->timemodified = time();
    switch ($data->type_select) {
        case 'Driver': // For a Driver record, the passengers and departure date need to be encoded.
            $data->driver = 0; // This field will be set once we know the id of this record.
            $data->passengers = json_encode($data->passengers ?? array());
            $data->departure_date = generate_timestamp($data, 'departure');
            break;
        case 'Passenger': // For a Passenger record, the destination and departure fields are inherited.
            unset($data->passengers);
            unset($data->destination);
            unset($data->date);
            break;
        case 'Other': // For an Other record, the other text is stored in the type field.
            $data->type_select = $data->type_other;
        default: // For Parent, Rideshare, and Other records, the destination and departure fields are saved on their own record.
            $data->driver = 0; // This field will be set once we know the id of this record.
            unset($data->passengers);
            $data->departure_date = generate_timestamp($data, 'departure');
    }
    $id = update_record($queryfields, $data);
    if ($data->type_select !== 'Passenger') { // For Driver, Parent, Rideshare and Other records, driverid references the same id.
        $record = $DB->get_record('local_signout_off_campus', array('id' => $id));
        $record->driverid = $id;
        $DB->update_record('local_signout_off_campus', $record);
    }
    $result = (new local_signout\local\off_campus\submitted($id))->send();
    logged_redirect(
        $form->get_redirect(), get_string('off_campus_success', 'local_signout'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$bottominstructions = get_config('local_signout', 'off_campus_form_instructions_bottom');
$bottominstructions = str_replace(
    '{minutes}', get_config('local_signout', 'off_campus_edit_window'), $bottominstructions
);
$formrenderable = new local_mxschool\output\form($form, false, $bottominstructions);
$jsrenderable = new local_mxschool\output\amd_module('local_signout/off_campus_form');

echo $output->header();
if ($isstudent && !validate_ip_off_campus()) {
    echo $output->heading(get_config('local_signout', 'off_campus_form_iperror'));
} else {
    echo $output->heading(
        $isstudent ? get_string('off_campus_form_title', 'local_signout', format_student_name($USER->id)) : $PAGE->title
    );
    echo $output->render($formrenderable);
    echo $output->render($jsrenderable);
}
echo $output->footer();
