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
 * Page for students to submit deans permission requests for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  deans_permission
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_deans_permission', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('form', 'deans_permission');

$queryfields = array(
    'local_mxschool_deans_perm' => array(
        'abbreviation' => 'dp',
        'fields' => array(
            'id', 'userid' => 'student', 'event', 'sport', 'missing_sports', 'missing_studyhours',
		  'missing_class', 'departure_time', 'return_time'
        )
    )
);

$data->isstudent = $isstudent ? '1' : '0';
$students = get_student_list();
$roomable = array(0 => get_string('form:select:default', 'local_mxschool')) + get_boarding_next_year_student_list();
$roomtypes = array(0 => get_string('form:select:default', 'local_mxschool')) + get_room_type_list();

$form = new local_mxschool\local\deans_permission\form(array('students' => $students));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->timemodified = time();
    $data->departure_time = generate_timestamp($data, 'departure');
    $data->return_time = generate_timestamp($data, 'return');
    $id = update_record($queryfields, $data);
    logged_redirect(
        $form->get_redirect(), get_string('deans_permission:form:success', 'local_mxschool'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading(
    $isstudent ? get_string('deans_permission_form_title', 'local_mxschool', format_student_name($USER->id)) : $PAGE->title
);
echo $output->render($renderable);
echo $output->footer();
