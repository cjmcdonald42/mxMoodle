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
 * Middlesex's Health Pass Form.
 *
 * @package     local_mxschool
 * @subpackage  healthpass
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

***










require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_advisor_selection', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('form', 'advisor_selection');
$PAGE->requires->js_call_amd('local_mxschool/advisor_selection_form', 'setup');

$queryfields = array(
    'local_mxschool_adv_selection' => array(
        'abbreviation' => 'asf',
        'fields' => array(
            'id', 'userid' => 'student', 'keep_current', 'option1id' => 'option1', 'option2id' => 'option2',
            'option3id' => 'option3', 'option4id' => 'option4', 'option5id' => 'option5', 'selectedid' => 'selected',
            'time_created' => 'timecreated', 'time_modified' => 'timemodified'
        )
    )
);

if ($isstudent && !student_may_access_advisor_selection($USER->id)) {
    redirect_to_fallback();
}
if ($id) { // Updating an existing record.
    if (!$DB->record_exists('local_mxschool_adv_selection', array('id' => $id))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, "asf.id = ?", array($id));
    if ($isstudent && $data->student !== $USER->id) { // Students can only edit their own forms.
        redirect($PAGE->url);
    }
} else { // Creating a new record.
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = time();
    $data->keep_current = 1;
    if ($isstudent) {
        $existingid = $DB->get_field('local_mxschool_adv_selection', 'id', array('userid' => $USER->id));
        if ($existingid) { // There can only be one advisor selection form per student.
            redirect(new moodle_url($PAGE->url, array('id' => $existingid)));
        }
        $data->student = $USER->id;
    }
}
$data->isstudent = $isstudent ? '1' : '0';
$data->warning = get_config('local_mxschool', 'advisor_form_closing_warning');
$data->instructions = get_config('local_mxschool', 'advisor_form_instructions');
$students = get_student_with_advisor_form_enabled_list();
$faculty = array(0 => get_string('form:select:default', 'local_mxschool')) + get_faculty_list();

$form = new local_mxschool\local\advisor_selection\form(array('students' => $students, 'faculty' => $faculty));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $current = $DB->get_field('local_mxschool_student', 'advisorid', array('userid' => $data->student));
    $data->timemodified = time();
    if ($data->keep_current) {
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
    $result = (new local_mxschool\local\advisor_selection\submitted($id))->send();
    logged_redirect(
        $form->get_redirect(), get_string('advisor_selection:form:success', 'local_mxschool'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading(
    $isstudent ? get_string('advisor_selection_form_title', 'local_mxschool', format_student_name($USER->id)) : $PAGE->title
);
echo $output->render($renderable);
echo $output->footer();
