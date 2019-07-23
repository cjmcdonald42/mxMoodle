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
 * Page for students to select advisors for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  advisor_selection
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/notification/advisor_selection.php');
require_once(__DIR__.'/advisor_form.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_advisor_selection', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('form', 'advisor_selection');

$queryfields = array('local_mxschool_adv_selection' => array('abbreviation' => 'asf', 'fields' => array(
    'id', 'userid' => 'student', 'keep_current' => 'keepcurrent', 'option1id' => 'option1', 'option2id' => 'option2',
    'option3id' => 'option3', 'option4id' => 'option4', 'option5id' => 'option5', 'selectedid' => 'selected',
    'time_created' => 'timecreated', 'time_modified' => 'timemodified'
)));

if ($isstudent && !student_may_access_advisor_selection($USER->id)) {
    redirect_to_fallback();
}
if ($id) {
    if (!$DB->record_exists('local_mxschool_adv_selection', array('id' => $id))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, "asf.id = ?", array($id));
    if ($isstudent && $data->student !== $USER->id) { // Students can only edit their own forms.
        redirect($PAGE->url);
    }
} else {
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = time();
    $data->keepcurrent = 1;
    if ($isstudent) {
        $existingid = $DB->get_field('local_mxschool_adv_selection', 'id', array('userid' => $USER->id));
        if ($existingid) { // There can only be one advisor selection form per student.
            redirect(new moodle_url($PAGE->url, array('id' => $existingid)));
        }
        $data->student = $USER->id;
    }
}
if (isset($data->student)) {
    $current = $DB->get_field('local_mxschool_student', 'advisorid', array('userid' => $data->student));
    $data->current = format_faculty_name($current);
}
$data->isstudent = $isstudent ? '1' : '0';
$data->warning = get_config('local_mxschool', 'advisor_form_closing_warning');
$data->instructions = get_config('local_mxschool', 'advisor_form_instructions');
$students = get_student_with_advisor_form_enabled_list();
$faculty = array(0 => get_string('form_select_default', 'local_mxschool')) + get_faculty_list();

$form = new advisor_form(array('id' => $id, 'students' => $students, 'faculty' => $faculty));
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
echo $output->heading(
    $isstudent ? get_string('advisor_selection_form_title', 'local_mxschool', format_student_name($USER->id)) : $PAGE->title
);
echo $output->render($formrenderable);
echo $output->render($jsrenderable);
echo $output->footer();
