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
 * Page for students to submit rooming requests for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  rooming
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
    require_capability('local/mxschool:manage_rooming', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('form', 'rooming');
$PAGE->requires->js_call_amd('local_mxschool/rooming_form', 'setup');


$queryfields = array(
    'local_mxschool_rooming' => array(
        'abbreviation' => 'r',
        'fields' => array(
            'id', 'userid' => 'student', 'room_type' => 'roomtype', 'dormmate1id' => 'dormmate1', 'dormmate2id' => 'dormmate2',
            'dormmate3id' => 'dormmate3', 'dormmate4id' => 'dormmate4', 'dormmate5id' => 'dormmate5', 'dormmate6id' => 'dormmate6',
            'has_lived_in_double' => 'liveddouble', 'preferred_roommateid' => 'roommate', 'time_created' => 'timecreated',
            'time_modified' => 'timemodified'
        )
    )
);

if ($isstudent && !student_may_access_rooming($USER->id)) {
    redirect_to_fallback();
}
if ($id) { // Updating an existing record.
    if (!$DB->record_exists('local_mxschool_rooming', array('id' => $id))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, "r.id = ?", array($id));
    if ($isstudent && $data->student !== $USER->id) { // Students can only edit their own forms.
        redirect($PAGE->url);
    }
} else { // Creating a new record.
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = time();
    $data->liveddouble = '-1'; // Invalid default to prevent auto selection.
    if ($isstudent) {
        $existingid = $DB->get_field('local_mxschool_rooming', 'id', array('userid' => $USER->id));
        if ($existingid) { // There can only be one rooming form per student.
            redirect(new moodle_url($PAGE->url, array('id' => $existingid)));
        }
        $data->student = $USER->id;
    }
}
$data->isstudent = $isstudent ? '1' : '0';
$data->instructions = get_config('local_mxschool', 'rooming_form_roommate_instructions');
$students = get_boarding_next_year_student_list();
$roomable = array(0 => get_string('form:select:default', 'local_mxschool')) + get_boarding_next_year_student_list();
$roomtypes = array(0 => get_string('form:select:default', 'local_mxschool')) + get_room_type_list();

$form = new local_mxschool\local\rooming\form(array('students' => $students, 'roomable' => $roomable, 'roomtypes' => $roomtypes));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->timemodified = time();
    $id = update_record($queryfields, $data);
    $result = (new local_mxschool\local\rooming\submitted($id))->send();
    logged_redirect(
        $form->get_redirect(), get_string('rooming:form:success', 'local_mxschool'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading(
    $isstudent ? get_string('rooming_form_title', 'local_mxschool', format_student_name($USER->id)) : $PAGE->title
);
echo $output->render($renderable);
echo $output->footer();
