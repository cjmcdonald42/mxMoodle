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
 * Page for students to sign out to an on-campus location for Middlesex's eSignout Subplugin.
 *
 * @package    local_signout
 * @subpackage on_campus
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../../mxschool/classes/output/renderable.php');
require_once(__DIR__.'/on_campus_form.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/signout:manage_on_campus', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('form', 'on_campus', 'signout');

$queryfields = array('local_signout_on_campus' => array('abbreviation' => 'oc', 'fields' => array(
    'id', 'userid' => 'student', 'locationid' => 'location_select', 'other' => 'location_other', 'time_created' => 'timecreated',
    'time_modified' => 'timemodified'
)));

if ($isstudent && !student_may_access_on_campus_signout($USER->id)) {
    redirect_to_fallback();
}
if ($id) {
    if (!$DB->record_exists('local_signout_on_campus', array('id' => $id))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, "oc.id = ?", array($id));
    if ($isstudent) { // Students cannot edit existing on-campus signout records.
        redirect($PAGE->url);
    }
} else {
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = time();
    if ($isstudent) {
        $data->student = $USER->id;
    }
}
$data->isstudent = $isstudent ? '1' : '0';
$data->locationwarning = get_config('local_signout', 'on_campus_form_warning_underclassmen');
$data->locationwarning11 = get_config('local_signout', 'on_campus_form_warning_juniors');
$students = get_student_list();
$locations = array(0 => get_string('form_select_default', 'local_mxschool')) + get_on_campus_location_list()
           + array(-1 => get_string('on_campus_form_location_select_other', 'local_signout'));

$form = new on_campus_form(array('id' => $id, 'students' => $students, 'locations' => $locations));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->timemodified = time();
    if ($data->location_select !== '-1') {
        unset($data->location_other);
    }
    update_record($queryfields, $data);
    logged_redirect(
        $form->get_redirect(), get_string('on_campus_success', 'local_signout'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$formrenderable = new \local_mxschool\output\form($form);
$jsrenderable = new \local_mxschool\output\amd_module('local_signout/on_campus_form');

echo $output->header();
if (!$isstudent || validate_ip('on_campus')) {
    echo $output->heading(
        $isstudent ? get_string('on_campus_form_title', 'local_signout', format_student_name($USER->id)) : $PAGE->title
    );
    echo $output->render($formrenderable);
    echo $output->render($jsrenderable);
} else {
    echo $output->heading(get_config('local_signout', 'on_campus_form_iperror'));
}
echo $output->footer();
