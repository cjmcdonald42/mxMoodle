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
 * Student edit page for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  user_management
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_students', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

setup_edit_page('student_edit', 'student_report', 'user_management');

$queryfields = array(
    'local_mxschool_student' => array('abbreviation' => 's', 'fields' => array(
        'id', 'phone_number' => 'phonenumber', 'birthday', 'admission_year' => 'admissionyear', 'grade', 'gender',
        'advisorid' => 'advisor', 'boarding_status' => 'isboarder', 'boarding_status_next_year' => 'isboardernextyear',
        'dormid' => 'dorm', 'room', 'picture_filename' => 'picture'
    )),
    'user' => array('abbreviation' => 'u', 'join' => 's.userid = u.id', 'fields' => array(
        'id' => 'userid', 'firstname', 'middlename', 'lastname', 'alternatename', 'email'
    )),
    'local_mxschool_permissions' => array('abbreviation' => 'p', 'join' => 's.userid = p.userid', 'fields' => array(
        'id' => 'permissionsid', 'overnight', 'license_date' => 'license', 'may_drive_to_town' => 'driving',
        'may_drive_passengers' => 'passengers', 'may_ride_with' => 'riding', 'ride_permission_details' => 'ridingcomment',
        'ride_share' => 'rideshare', 'may_drive_to_boston' => 'boston', 'swim_competent' => 'swimcompetent',
        'swim_allowed' => 'swimallowed', 'boat_allowed' => 'boatallowed'
    ))
);

if (!$DB->record_exists('local_mxschool_student', array('id' => $id))) {
    redirect_to_fallback();
}
$ridingencode = array(
    'Parent Permission' => 'parent', 'Over 21' => '21', 'Any Driver' => 'any', 'Specific Drivers' => 'specific'
);
$data = get_record($queryfields, "s.id = ?", array($id));
$data->riding = isset($data->riding) ? $ridingencode[$data->riding] : null;
$dorms = get_dorm_list();
$faculty = get_faculty_list();

$form = new local_mxschool\local\user_management\student_edit_form(array('dorms' => $dorms, 'faculty' => $faculty));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    if (!$data->room) {
        unset($data->room);
    }
    if (!$data->picture) {
        unset($data->picture);
    }
    if (!$data->license) {
        unset($data->license);
    }
    if (!isset($data->riding) || $data->riding !== 'specific') {
        unset($data->ridingcomment);
    }
    $data->riding = isset($data->riding) ? array_flip($ridingencode)[$data->riding] : null;
    update_record($queryfields, $data);
    logged_redirect($form->get_redirect(), get_string('user_management_student_update_success', 'local_mxschool'), 'update');
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
