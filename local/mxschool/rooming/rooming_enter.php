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
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once('rooming_form.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_rooming', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('rooming', 'local_mxschool') => '/local/mxschool/rooming/index.php'
);
$redirect = new moodle_url($parents[array_keys($parents)[count($parents) - 1]]);
$url = '/local/mxschool/rooming/rooming_enter.php';
$title = get_string('rooming_form', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$queryfields = array('local_mxschool_rooming' => array('abbreviation' => 'r', 'fields' => array(
    'id', 'userid' => 'student', 'room_type' => 'roomtype', 'dormmate1id' => 'dormmate1', 'dormmate2id' => 'dormmate2',
    'dormmate3id' => 'dormmate4', 'dormmate4id' => 'dormmate5', 'dormmate5id' => 'dormmate6',
    'has_lived_in_double' => 'liveddouble', 'preferred_roommateid' => 'roommate', 'time_created' => 'timecreated',
    'time_modified' => 'timemodified'
)));

if ($id) {
    if (!$DB->record_exists('local_mxschool_rooming', array('id' => $id))) {
        redirect($redirect);
    }
    $data = get_record($queryfields, "r.id = ?", array($id));
    if ($isstudent && $data->student !== $USER->id) { // Students can only edit their own forms.
        redirect(new moodle_url($url));
    }
} else {
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = time();
    if ($isstudent) {
        $existingid = $DB->get_field('local_mxschool_rooming', 'id', array('userid' => $USER->id));
        if ($existingid) { // There can only be one rooming form per student.
            redirect(new moodle_url($url, array('id' => $existingid)));
        }
        $data->student = $USER->id;
    }
}
if ($isstudent) {
    $record = $DB->get_record_sql(
        "SELECT CONCAT(u.firstname, ' ', u.lastname) AS student FROM {user} u WHERE u.id = ?", array($USER->id)
    );
}
$data->isstudent = $isstudent ? '1' : '0';
$data->dorm = isset($data->student) ? $DB->get_field_sql(
    "SELECT d.name FROM {local_mxschool_student} s LEFT JOIN {local_mxschool_dorm} d ON s.dormid = d.id WHERE s.userid = ?",
    array($data->student)
) : '';
$data->instructions = get_config('local_mxschool', 'rooming_form_roommate_instructions');
$students = get_boarding_next_year_student_list();
$roomable = array(0 => get_string('rooming_form_roomable_default', 'local_mxschool')) + get_boarding_next_year_student_list();

$form = new rooming_form(array('id' => $id, 'students' => $students, 'roomable' => $roomable));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->timemodified = time();
    update_record($queryfields, $data);
    redirect(
        $form->get_redirect(), get_string('rooming_success', 'local_mxschool'), null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form($form);
$jsrenderable = new \local_mxschool\output\amd_module('local_mxschool/get_rooming_student_options');

echo $output->header();
echo $output->heading($title.($isstudent ? " for {$record->student}" : ''));
echo $output->render($renderable);
echo $output->render($jsrenderable);
echo $output->footer();
