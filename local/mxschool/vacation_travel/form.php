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
 * Page for students to submit vacation travel plans and transportation needs for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  vacation_travel
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
    require_capability('local/mxschool:manage_vacation_travel', context_system::instance());
}

$returnenabled = get_config('local_mxschool', 'vacation_form_returnenabled');
$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('form', 'vacation_travel');

$tripqueryfields = array('local_mxschool_vt_trip' => array('abbreviation' => 't', 'fields' => array(
    'id', 'userid' => 'student', 'departureid', 'returnid', 'destination', 'phone_number' => 'phone',
    'time_created' => 'timecreated', 'time_modified' => 'timemodified'
)));
$transportqueryfields = array('local_mxschool_vt_transport' => array('abbreviation' => 'dr', 'fields' => array(
    'id', 'mx_transportation' => 'mxtransportation', 'type', 'siteid' => 'site', 'details', 'carrier',
    'transportation_number' => 'number', 'date_time' => 'variable_date', 'international'
)));

if ($isstudent && !student_may_access_vacation_travel($USER->id)) {
    redirect_to_fallback();
}
if ($id) { // Updating an existing record.
    if (!$DB->record_exists('local_mxschool_vt_trip', array('id' => $id))) {
        redirect_to_fallback();
    }
    $data = get_record($tripqueryfields, 't.id = ?', array($id));
    if ($isstudent && $data->student !== $USER->id) { // Students can only edit their own forms.
        redirect($PAGE->url);
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
        if ($returndata) {
            foreach ($returndata as $key => $value) {
                $data->{"ret_{$key}"} = $value;
            }
            if (!isset($data->ret_mxtransportation)) {
                $data->ret_mxtransportation = '-1'; // Invalid default to prevent auto selection.
            }
            if (!isset($data->ret_international)) {
                $data->ret_international = '-1'; // Invalid default to prevent auto selection.
            }
        } else {
            $data->ret_id = '0';
            $data->ret_mxtransportation = '-1'; // Invalid default to prevent auto selection.
            $data->ret_site = '-1'; // Invalid default to prevent auto selection.
            $data->ret_variable_date = time();
            $data->ret_international = '-1'; // Invalid default to prevent auto selection.
        }
    }
} else { // Creating a new record.
    $data = new stdClass();
    $data->id = $data->dep_id = $data->ret_id = $id;
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
            redirect(new moodle_url($PAGE->url, array('id' => $existingid)));
        }
        $data->student = $USER->id;
    }
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

$form = new local_mxschool\local\vacation_travel\form(array(
    'returnenabled' => $returnenabled, 'students' => $students, 'depsites' => $depsites, 'retsites' => $retsites, 'types' => $types
));
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
        unset($departuredata->site);
        unset($departuredata->international);
    }
    if ($departuredata->type !== 'Car' && $departuredata->type !== 'Non-MX Bus' && $departuredata->site !== '0') {
        unset($departuredata->details);
    }
    if ($departuredata->type !== 'Plane' && $departuredata->type !== 'Train' && $departuredata->type !== 'Bus') {
        unset($departuredata->carrier);
        unset($departuredata->number);
    }
    if ($departuredata->type !== 'Plane') {
        unset($departuredata->international);
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
            unset($returndata->site);
            unset($returndata->international);
        }
        if ($returndata->type !== 'Car' && $returndata->type !== 'Non-MX Bus' && $returndata->site !== '0') {
            unset($returndata->details);
        }
        if ($returndata->type !== 'Plane' && $returndata->type !== 'Train' && $returndata->type !== 'Bus') {
            unset($returndata->carrier);
            unset($returndata->number);
        }
        if ($returndata->type !== 'Plane') {
            unset($returndata->international);
        }
        $data->returnid = update_record($transportqueryfields, $returndata);
    } else {
        unset($data->returnid);
    }
    $id = update_record($tripqueryfields, $data);
    $result = (new local_mxschool\local\vacation_travel\submitted($id))->send();
    logged_redirect(
        $form->get_redirect(), get_string('vacation_success', 'local_mxschool'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);
$jsrenderable = new local_mxschool\output\amd_module('local_mxschool/vacation_travel_form');

echo $output->header();
echo $output->heading(
    $isstudent ? get_string('vacation_travel_form_title', 'local_mxschool', format_student_name($USER->id)) : $PAGE->title
);
echo $output->render($renderable);
echo $output->render($jsrenderable);
echo $output->footer();
