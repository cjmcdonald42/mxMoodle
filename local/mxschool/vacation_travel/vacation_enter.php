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
 * Page for students to request rooming for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage vacation_travel
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/mx_notifications.php');
require_once('vacation_form.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_vacation_travel', context_system::instance());
}

$returnenabled = get_config('local_mxschool', 'vacation_form_returnenabled');
$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('vacation_travel', 'local_mxschool') => '/local/mxschool/vacation_travel/index.php'
);
$redirect = get_redirect($parents);
$url = '/local/mxschool/vacation_travel/vacation_enter.php';
$title = get_string('vacation_travel_form', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$tripqueryfields = array('local_mxschool_vt_trip' => array('abbreviation' => 't', 'fields' => array(
    'id', 'userid' => 'student', 'departureid', 'returnid', 'destination', 'phone_number' => 'phone',
    'time_created' => 'timecreated', 'time_modified' => 'timemodified'
)));
$transportqueryfields = array('local_mxschool_vt_transport' => array('abbreviation' => 'dr', 'fields' => array(
    'id', 'mx_transportation' => 'mxtransportation', 'type', 'siteid' => 'site', 'details', 'carrier',
    'transportation_number' => 'number', 'date_time' => 'variable_date', 'international'
)));

if ($isstudent && !student_may_access_vacation_travel($USER->id)) {
    redirect($redirect);
}
if ($id) {
    if (!$DB->record_exists('local_mxschool_vt_trip', array('id' => $id))) {
        redirect($redirect);
    }
    $data = get_record($tripqueryfields, 't.id = ?', array($id));
    if ($isstudent && $data->student !== $USER->id) { // Students can only edit their own forms.
        redirect(new moodle_url($url));
    }
    $departuredata = get_record($transportqueryfields, 'dr.id = ?', array($data->departureid));
    foreach ($departuredata as $key => $value) {
        $data->{"dep_{$key}"} = $value;
    }
    if (!isset($data->dep_international)) {
        $data->dep_international = '-1'; // Invalid default to prevent auto selection.
    }
    if ($returnenabled) {
        $returndata = get_record($transportqueryfields, 'dr.id = ?', array($data->returnid));
        foreach ($returndata as $key => $value) {
            $data->{"ret_{$key}"} = $value;
        }
        if (!isset($data->ret_mxtransportation)) {
            $data->ret_mxtransportation = '-1'; // Invalid default to prevent auto selection.
        }
        if (!isset($data->ret_international)) {
            $data->ret_international = '-1'; // Invalid default to prevent auto selection.
        }
    }
} else {
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = time();
    $data->dep_mxtransportation = '-1'; // Invalid default to prevent auto selection.
    $data->dep_site = '-1'; // Invalid default to prevent auto selection.
    $data->dep_variable_date = time();
    $data->dep_international = '-1'; // Invalid default to prevent auto selection.
    if ($returnenabled) {
        $data->ret_mxtransportation = '-1'; // Invalid default to prevent auto selection.
        $data->ret_site = '-1'; // Invalid default to prevent auto selection.
        $data->ret_variable_date = time();
        $data->ret_international = '-1'; // Invalid default to prevent auto selection.
    }
    if ($isstudent) {
        $existingid = $DB->get_field('local_mxschool_vt_trip', 'id', array('userid' => $USER->id));
        if ($existingid) { // There can only be one vacation travel form per student.
            redirect(new moodle_url($url, array('id' => $existingid)));
        }
        $data->student = $USER->id;
    }
}
if ($isstudent) {
    $record = $DB->get_record_sql(
        "SELECT CONCAT(u.lastname, ', ', u.firstname) AS student, u.firstname, u.alternatename FROM {user} u WHERE u.id = ?",
        array($USER->id)
    );
    $record->student = $record->student.(
        $record->alternatename && $record->alternatename !== $record->firstname ? " ({$record->alternatename})" : ''
    );
}
$data->isstudent = $isstudent ? '1' : '0';
generate_time_selector_fields($data, 'dep_variable', 15);
if ($returnenabled) {
    generate_time_selector_fields($data, 'ret_variable', 15);
}
$students = get_boarding_student_list();
$depsites = get_vacation_travel_departure_sites_list();
$retsites = get_vacation_travel_return_sites_list();
$types = get_vacation_travel_type_list();

$form = new vacation_form(array(
    'id' => $id, 'returnenabled' => $returnenabled, 'students' => $students, 'depsites' => $depsites, 'retsites' => $retsites,
    'types' => $types
));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->timemodified = time();
    $departuredata = new stdClass();
    $returndata = new stdClass();
    foreach ($data as $key => $value) {
        $section = strlen($key) >= 3 ? substr($key, 0, 3) : '';
        if ($section === 'dep') {
            $departuredata->{substr($key, 4)} = $value;
        } else if ($section === 'ret') {
            $returndata->{substr($key, 4)} = $value;
        }
    }
    $departuredata->variable_date = generate_timestamp($departuredata, 'variable');
    if (isset($departuredata->site)) {
        $departuredefault = $DB->get_field(
            'local_mxschool_vt_site', 'default_departure_time', array('id' => $departuredata->site)
        );
        if ($departuredefault) { // If there is a default time, the time from the form is nonsense.
            $departuredata->variable_date = $departuredefault;
        }
    }
    if (!$departuredata->mxtransportation) {
        $departuredata->site = null;
        $departuredata->international = null;
    }
    if ($departuredata->type !== 'Car' && $departuredata->type !== 'Non-MX Bus' && $departuredata->site !== '0') {
        $departuredata->details = null;
    }
    if ($departuredata->type !== 'Plane' && $departuredata->type !== 'Train' && $departuredata->type !== 'Bus') {
        $departuredata->carrier = null;
        $departuredata->number = null;
    }
    if ($departuredata->type !== 'Plane') {
        $departuredata->international = null;
    }
    $data->departureid = update_record($transportqueryfields, $departuredata);
    if ($returnenabled) {
        $returndata->variable_date = generate_timestamp($returndata, 'variable');
        if (isset($returndata->site)) {
            $returndefault = $DB->get_field(
                'local_mxschool_vt_site', 'default_return_time', array('id' => $returndata->site)
            );
            if ($returndefault) { // If there is a default time, the time from the form is nonsense.
                $returndata->variable_date = $returndefault;
            }
        }
        if (!$returndata->mxtransportation) {
            $returndata->site = null;
            $returndata->international = null;
        }
        if ($returndata->type !== 'Car' && $returndata->type !== 'Non-MX Bus' && $returndata->site !== '0') {
            $returndata->details = null;
        }
        if ($returndata->type !== 'Plane' && $returndata->type !== 'Train' && $returndata->type !== 'Bus') {
            $returndata->carrier = null;
            $returndata->number = null;
        }
        if ($returndata->type !== 'Plane') {
            $returndata->international = null;
        }
        $data->returnid = update_record($transportqueryfields, $returndata);
    } else {
        $data->returnid = 0;
    }
    $id = update_record($tripqueryfields, $data);
    $result = mx_notifications::send_email('vacation_travel_submitted', array('id' => $id));
    logged_redirect(
        $form->get_redirect(), get_string('vacation_success', 'local_mxschool'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form($form);
$jsrenderable = new \local_mxschool\output\amd_module('local_mxschool/vacation_travel_form');

echo $output->header();
echo $output->heading($title.($isstudent ? " for {$record->student}" : ''));
echo $output->render($renderable);
echo $output->render($jsrenderable);
echo $output->footer();
