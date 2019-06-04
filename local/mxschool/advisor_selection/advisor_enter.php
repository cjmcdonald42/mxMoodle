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
 * Page for students to select advisors for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/notification/advisor_selection.php');
require_once('advisor_form.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_advisor_selection', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('advisor_selection', 'local_mxschool') => '/local/mxschool/advisor_selection/index.php'
);
$redirect = get_redirect($parents);
$url = '/local/mxschool/advisor_selection/advisor_enter.php';
$title = get_string('advisor_selection_form', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$queryfields = array('local_mxschool_adv_selection' => array('abbreviation' => 'asf', 'fields' => array(
    'id', 'userid' => 'student', 'keep_current' => 'keepcurrent', 'option1id' => 'option1', 'option2id' => 'option2',
    'option3id' => 'option3', 'option4id' => 'option4', 'option5id' => 'option5', 'selectedid' => 'selected',
    'time_created' => 'timecreated', 'time_modified' => 'timemodified'
)));

if ($isstudent && !student_may_access_advisor_selection($USER->id)) {
    redirect($redirect);
}
if ($id) {
    if (!$DB->record_exists('local_mxschool_adv_selection', array('id' => $id))) {
        redirect($redirect);
    }
    $data = get_record($queryfields, "asf.id = ?", array($id));
    if ($isstudent && $data->student !== $USER->id) { // Students can only edit their own forms.
        redirect(new moodle_url($url));
    }
} else {
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = time();
    $data->keepcurrent = 1;
    if ($isstudent) {
        $existingid = $DB->get_field('local_mxschool_adv_selection', 'id', array('userid' => $USER->id));
        if ($existingid) { // There can only be one advisor selection form per student.
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
    $record->student = $record->student . (
        $record->alternatename && $record->alternatename !== $record->firstname ? " ({$record->alternatename})" : ''
    );
}
$data->isstudent = $isstudent ? '1' : '0';
$data->current = isset($data->student) ? $DB->get_field_sql(
    "SELECT CONCAT(u.lastname, ', ', u.firstname)
     FROM {local_mxschool_student} s
     LEFT JOIN {user} u ON s.advisorid = u.id
     WHERE s.userid = ?", array($data->student)
) : '';
$data->warning = get_config('local_mxschool', 'advisor_form_closing_warning');
$data->instructions = get_config('local_mxschool', 'advisor_form_instructions');
$students = get_student_with_advisor_form_enabled_list();
$faculty = array(0 => get_string('form_select_default', 'local_mxschool')) + get_faculty_list();

$form = new advisor_form(array('id' => $id, 'students' => $students, 'faculty' => $faculty));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $current = $DB->get_field('local_mxschool_student', 'advisorid', array('userid' => $data->student));
    $data->timemodified = time();
    if ($data->keepcurrent) {
        $discardfrom = 1;
    } else {
        for ($i = 1; $i <= 5; $i++) {
            if (!$data->{"option{$i}"}) {
                $discardfrom = $i;
                break;
            }
            if ($data->{"option{$i}"} === $current) {
                $discardfrom = $i + 1;
                break;
            }
        }
    }
    if (isset($discardfrom)) {
        for ($i = $discardfrom; $i <= 5; $i++) {
            $data->{"option{$i}"} = 0;
        }
    }
    if (!isset($data->selected)) {
        $data->selected = 0;
    }
    $id = update_record($queryfields, $data);
    $result = (new \local_mxschool\local\advisor_selection\submitted($id))->send();
    logged_redirect(
        $form->get_redirect(), get_string('advisor_selection_success', 'local_mxschool'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$formrenderable = new \local_mxschool\output\form($form);
$jsrenderable = new \local_mxschool\output\amd_module('local_mxschool/advisor_selection_form');

echo $output->header();
echo $output->heading($title . ($isstudent ? " for {$record->student}" : ''));
echo $output->render($formrenderable);
echo $output->render($jsrenderable);
echo $output->footer();
