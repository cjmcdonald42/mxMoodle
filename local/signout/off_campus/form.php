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
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/signout:manage_off_campus', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('form', 'off_campus', 'signout');
if (!$isstudent || validate_ip_off_campus()) {
    $PAGE->requires->js_call_amd('local_signout/off_campus_form', 'setup');
}

$queryfields = array(
    'local_signout_off_campus' => array(
        'abbreviation' => 'oc',
        'fields' => array(
            'id', 'userid' => 'student', 'typeid' => 'type_select', 'other' => 'type_other', 'driverid' => 'driver',
            'approverid' => 'approver', 'passengers', 'destination', 'departure_time' => 'departure_date',
            'time_created' => 'timecreated', 'time_modified' => 'timemodified'
        )
    )
);

if ($isstudent && !student_may_access_off_campus_signout($USER->id)) {
    redirect_to_fallback();
}
if ($id) { // Updating an existing record.
    if (!$DB->record_exists('local_signout_off_campus', array('id' => $id))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, "oc.id = ?", array($id));
    if ($isstudent && (generate_datetime()->getTimestamp() > get_edit_cutoff($data->timecreated) || $data->student !== $USER->id)) {
        // Students cannot edit existing off-campus signout records beyond the edit window.
        redirect_to_fallback();
    }
    $permissions = $DB->get_field('local_signout_type', 'required_permissions', array('id' => $data->type_select));
    if ($permissions === 'driver') {
        $data->passengers = json_decode($data->passengers);
    }
} else { // Creating a new record.
    $currentsignout = get_user_current_signout();
    if ($isstudent && $currentsignout && $currentsignout->type = 'off_campus') {
        // Students cannot create a new record if they already have an active one.
        redirect(new moodle_url($PAGE->url, array('id' => $currentsignout->id)));
    }
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = $data->departure_date = time();
    if ($isstudent) {
        $data->student = $USER->id;
    }
}
$data->isstudent = $isstudent ? '1' : '0';
$data->instructions = get_config('local_signout', 'off_campus_form_instructions_passenger');
$data->driverwarning = get_config('local_signout', 'off_campus_form_warning_driver_nopassengers');
generate_time_selector_fields($data, 'departure', 15);
$students = get_student_list();
$types = array(0 => get_string('form:select:default', 'local_mxschool')) + get_off_campus_type_list()
       + array(-1 => get_string('off_campus_form_type_select_other', 'local_signout'));
$passengers = get_permitted_passenger_list();
$drivers = array(0 => get_string('form:select:default', 'local_mxschool')) + get_permitted_driver_list();
$approvers = array(0 => get_string('form:select:default', 'local_mxschool')) + get_approver_list();

$form = new local_signout\local\off_campus\form(array(
    'students' => $students, 'types' => $types, 'passengers' => $passengers, 'drivers' => $drivers, 'approvers' => $approvers
));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->timemodified = time();
    $permissions = $DB->get_field('local_signout_type', 'required_permissions', array('id' => $data->type_select));
    if ($permissions === 'passenger') { // Only passengers have drivers.
        $driver = $DB->get_record('local_signout_off_campus', array('id' => $data->driver));
        $data->destination = $driver->destination;
        $data->departure_date = $driver->departure_time;
    } else {
        unset($data->driver);
        $data->departure_date = generate_timestamp($data, 'departure');
    }
    if ($permissions === 'driver') { // Only drivers have passengers.
        $data->passengers = json_encode($data->passengers ?? array());
        $existingpassengers = $DB->get_records('local_signout_off_campus', array('driverid' => $data->id));
        if ($existingpassengers) { // Update any existing passengers when the driver is updated.
            foreach ($existingpassengers as $passenger) {
                $passenger->destination = $data->destination;
                $passenger->departure_time = $data->departure_date;
                $DB->update_record('local_signout_off_campus', $passenger);
            }
        }
    } else {
        unset($data->passengers);
    }
    if (!$permissions && $data->type_select !== '-1') {
        unset($data->approver);
    }
    if ($data->type_select !== '-1') { // Only keep the 'other' field if the type is 'other'.
        unset($data->type_other);
    }
    $id = update_record($queryfields, $data);
    $result = (new local_signout\local\off_campus\submitted($id))->send();
    logged_redirect(
        $form->get_redirect(), get_string('off_campus_success', 'local_signout'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_signout');
$bottominstructions = get_config('local_signout', 'off_campus_form_instructions_bottom');
$bottominstructions = str_replace(
    '{minutes}', get_config('local_signout', 'off_campus_edit_window'), $bottominstructions
);
$renderable = new local_mxschool\output\form($form, false, $bottominstructions);

echo $output->header();
if ($isstudent && !validate_ip_off_campus()) {
    echo $output->heading(get_config('local_signout', 'off_campus_form_iperror'));
} else {
    echo $output->heading(
        $isstudent ? get_string('off_campus_form_title', 'local_signout', format_student_name($USER->id)) : $PAGE->title
    );
    echo $output->render($renderable);
}
echo $output->footer();
