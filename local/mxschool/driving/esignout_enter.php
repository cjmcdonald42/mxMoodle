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
 * Page for students to sign out for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once('esignout_form.php');
require_once(__DIR__.'/../classes/mx_notifications.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/events/page_visited.php');
require_once(__DIR__.'/../locallib.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_esignout', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('driving', 'local_mxschool') => '/local/mxschool/driving/index.php'
);
$redirect = new moodle_url($parents[array_keys($parents)[count($parents) - 1]]);
$url = '/local/mxschool/driving/esignout_enter.php';
$title = get_string('esignout', 'local_mxschool');
$queryfields = array('local_mxschool_esignout' => array('abbreviation' => 'es', 'fields' => array(
    'id', 'userid' => 'student', 'driverid' => 'driver', 'approverid' => 'approver', 'destination',
    'departure_time' => 'departuretime', 'time_modified' => 'timemodified', 'time_created' => 'timecreated'
)));

if ($id) {
    if ($isstudent) { // Students cannot edit existing esignout records.
        redirect(new moodle_url($url));
    } else {
        if ($DB->record_exists('local_mxschool_esignout', array('id' => $id))) {
            $data = get_record($queryfields, "es.id = ?", array($id));
            if ($data->id === $data->driver) { // Existing passenger records should add any inherited values to the data object.
                $data->type = 'Driver';
            } else {
                $data->type = 'Passenger';
                $driver = get_record($queryfields, "es.id = ?", array($data->driver));
                if (!isset($data->destination)) {
                    $data->destination = $driver->destination;
                }
                if (!isset($data->departuretime)) {
                    $data->departuretime = $driver->departuretime;
                }
            }
        } else {
            redirect($redirect);
        }
    }
} else {
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = time();
    $data->type = 'Driver';
    if ($isstudent) {
        $data->student = $USER->id;
    }
}
$data->isstudent = $isstudent;
$students = get_student_list();
$drivers = get_current_drivers_list();
$approvers = get_approver_list();

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

$form = new esignout_form(null, array('id' => $id, 'students' => $students, 'drivers' => $drivers, 'approvers' => $approvers));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->timemodified = time();
    if ($data->type === 'Driver') {
        $data->driver = 0;
    }
    if ($data->type === 'Passenger') { // For a passenger record, the destination, departure, and return fields are inherited.
        $data->destination = null;
        $data->departuretime = null;
    }
    $id = update_record($queryfields, $data);
    if ($data->type === 'Driver') { // For a driver record, the id and driverid should be the same.
        $data->id = $data->driver = $id;
        $id = update_record($queryfields, $data);
    }
    redirect(
        $form->get_redirect(), get_string('esignout_success', 'local_mxschool'), null, \core\output\notification::NOTIFY_SUCCESS
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form_page($form);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
