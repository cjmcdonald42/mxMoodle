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
 * Page for students to submit a weekend form for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once('weekend_form.php');
require_once(__DIR__.'/../classes/mx_notifications.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/events/page_visited.php');
require_once(__DIR__.'/../locallib.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_weekend', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('checkin', 'local_mxschool') => '/local/mxschool/checkin/index.php'
);
$redirect = new moodle_url($parents[array_keys($parents)[count($parents) - 1]]);
$url = '/local/mxschool/checkin/weekend_enter.php';
$title = get_string('weekend_form', 'local_mxschool');
$queryfields = array('local_mxschool_weekend_form' => array('abbreviation' => 'wf', 'fields' => array(
    'id', 'userid' => 'student', 'weekendid' => 'weekend', 'departure_date_time' => 'departuretime',
    'return_date_time' => 'returntime', 'destination', 'transportation', 'phone_number' => 'phone',
    'time_created' => 'timecreated', 'time_modified' => 'timemodified'
)));

if ($id) {
    if (!$DB->record_exists('local_mxschool_weekend_form', array('id' => $id))) {
        redirect($redirect);
    }
    if ($isstudent) { // Students cannot edit existing weekend forms.
        redirect(new moodle_url($url));
    }
    $data = get_record($queryfields, "wf.id = ?", array($id));
    $data->dorm = $DB->get_field('local_mxschool_student', 'dormid', array('userid' => $data->student));
} else {
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = time();
    if ($isstudent) {
        $data->student = $USER->id;
        $record = $DB->get_record_sql(
            "SELECT CONCAT(u.firstname, ' ', u.lastname) AS student, d.id AS dorm,
                    CONCAT(hoh.firstname, ' ', hoh.lastname) AS hoh, d.permissions_line AS permissionsline
             FROM {local_mxschool_student} s
             LEFT JOIN {user} u ON s.userid = u.id
             LEFT JOIN {local_mxschool_dorm} d ON s.dormid = d.id
             LEFT JOIN {user} hoh ON d.hohid = hoh.id
             WHERE s.userid = ?", array($USER->id)
        );
        $data->dorm = $record->dorm;
    } else {
        $dorm = $DB->get_field('local_mxschool_faculty', 'dormid', array('userid' => $USER->id));
        if ($dorm) {
            $data->dorm = $dorm;
        }
    }
}
$data->isstudent = $isstudent ? '1' : '0';
$dorms = array('0' => get_string('report_select_dorm', 'local_mxschool')) + get_dorm_list();
$students = get_student_list();

$event = \local_mxschool\event\page_visited::create(array('other' => array('page' => $title)));
$event->trigger();

$PAGE->set_url(new moodle_url($url));
$PAGE->set_context(context_system::instance());
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('incourse');
foreach ($parents as $display => $parenturl) {
    $PAGE->navbar->add($display, new moodle_url($parenturl));
}
$PAGE->navbar->add($title);

$form = new weekend_form(array('id' => $id, 'dorms' => $dorms, 'students' => $students));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->timemodified = time();
    $data->weekend = $DB->get_field_sql(
        "SELECT id FROM {local_mxschool_weekend} WHERE ? > start_time AND ? < end_time",
        array($data->departuretime, $data->departuretime)
    );
    $id = update_record($queryfields, $data);
    $oldrecord = $DB->get_record_sql(
        "SELECT * FROM {local_mxschool_weekend_form} WHERE userid = ? AND weekendid = ? AND id != ? AND active = 1",
        array($data->student, $data->weekend, $id)
    );
    if ($oldrecord) {
        $oldrecord->active = 0; // Each student can have only one active record for a given weekend.
        $DB->update_record('local_mxschool_weekend_form', $oldrecord);
    }
    $result = mx_notifications::send_email('weekend_form_submitted', array('id' => $id));
    redirect(
        $form->get_redirect(), get_string('weekend_form_success', 'local_mxschool'), null, \core\output\notification::NOTIFY_SUCCESS
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$bottominstructions = get_config('local_mxschool', 'weekend_form_instructions_bottom');
$bottominstructions = str_replace(
    '{hoh}', $isstudent ? $record->hoh : get_string(
        'weekend_form_instructions_placeholder_hoh', 'local_mxschool'
    ), $bottominstructions
);
$bottominstructions = str_replace(
    '{permissionsline}', $isstudent ? $record->permissionsline : get_string(
        'weekend_form_instructions_placeholder_permissionsline', 'local_mxschool'
    ), $bottominstructions
);
$formrenderable = new \local_mxschool\output\form(
    $form, get_config('local_mxschool', 'weekend_form_instructions_top'), $bottominstructions
);
$jsrenderable = new \local_mxschool\output\amd_module('local_mxschool/get_dorm_students');

echo $output->header();
echo $output->heading($title.($isstudent ? " for {$record->student} ({$dorms[$record->dorm]})" : ''));
echo $output->render($formrenderable);
echo $output->render($jsrenderable);
echo $output->footer();
