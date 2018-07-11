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
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once('advisor_form.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/events/page_visited.php');
require_once(__DIR__.'/../locallib.php');

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
$redirect = new moodle_url($parents[array_keys($parents)[count($parents) - 1]]);
$url = '/local/mxschool/advisor_selection/advisor_enter.php';
$title = get_string('advisor_selection_form', 'local_mxschool');
$queryfields = array('local_mxschool_adv_selection' => array('abbreviation' => 'asf', 'fields' => array(
    'id', 'userid' => 'student', 'keep_current' => 'keepcurrent', 'option1id' => 'option1', 'option2id' => 'option2',
    'option3id' => 'option3', 'option4id' => 'option4', 'option5id' => 'option5', 'selectedid' => 'selected',
    'time_created' => 'timecreated', 'time_modified' => 'timemodified'
)));

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
        "SELECT CONCAT(u.firstname, ' ', u.lastname) AS student FROM {user} u WHERE u.id = ?", array($USER->id)
    );
}
$data->isstudent = $isstudent ? '1' : '0';
$data->current = isset($data->student) ? $DB->get_field_sql(
    "SELECT CONCAT(u.lastname, ', ', u.firstname)
     FROM {local_mxschool_student} s
     LEFT JOIN {user} u ON s.advisorid = u.id
     WHERE s.userid = ?", array($data->student)
) : '';

$students = get_student_list();
$faculty = array(0 => get_string('advisor_form_faculty_default', 'local_mxschool')) + get_faculty_list();

$event = \local_mxschool\event\page_visited::create(array('other' => array('page' => $title)));
$event->trigger();

$PAGE->set_url(new moodle_url($url));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('incourse');
foreach ($parents as $display => $url) {
    $PAGE->navbar->add($display, new moodle_url($url));
}
$PAGE->navbar->add($title);

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
    update_record($queryfields, $data);
    redirect(
        $form->get_redirect(), get_string('advisor_selection_success', 'local_mxschool'), null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$formrenderable = new \local_mxschool\output\form_page($form);
$jsrenderable = new \local_mxschool\output\js_module('local_mxschool/get_advisor_selection_student_options');

echo $output->header();
echo $output->heading($title.($isstudent ? " for {$record->student}" : ''));
echo $output->render($formrenderable);
echo $output->render($jsrenderable);
echo $output->footer();
