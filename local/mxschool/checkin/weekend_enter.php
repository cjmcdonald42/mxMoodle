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
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/events/page_visited.php');
require_once(__DIR__.'/../locallib.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_weekend', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

$parents = $parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('checkin', 'local_mxschool') => '/local/mxschool/checkin/index.php'
);
$redirect = new moodle_url($parents[array_keys($parents)[count($parents) - 1]]);
$url = '/local/mxschool/checkin/weekend_enter.php';
$title = get_string('weekend_form', 'local_mxschool');
$queryfields = array('local_mxschool_weekend_form' => array('abbreviation' => 'wf', 'fields' => array(
    'id', 'userid' => 'student', 'weekendid' => 'weekend', 'departure_date_time' => 'departuretime',
    'return_date_time' => 'returntime', 'destination', 'transportation', 'phone_number' => 'phone', 'time_modified'
)));
$students = get_student_list();

if (!$id) {
    $record = new stdClass();
    $record->userid = $isstudent ? $USER->id : 0;
    $record->departure_date_time = $record->return_date_time = $record->time_created = $record->time_modified = time();
    $id = $DB->insert_record('local_mxschool_weekend_form', $record);
    redirect(new moodle_url($url, array('id' => $id)));
}

if ($id && !$DB->record_exists('local_mxschool_weekend_form', array('id' => $id))) {
    redirect($redirect);
}

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

$form = new weekend_form(null, array('id' => $id, 'students' => $students));
$form->set_redirect($redirect);
$data = get_record($queryfields, "wf.id = ?", array($id));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->time_modified = time();
    $data->weekend = 0;
    // TODO: Calculate weekend and validate.
    update_record($queryfields, $data);
    // TODO: email notification.
    redirect(
        $form->get_redirect(), get_string('weekend_form_success', 'local_mxschool'), null, \core\output\notification::NOTIFY_SUCCESS
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form_page($form, get_string('weekend_form_description', 'local_mxschool'));

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
