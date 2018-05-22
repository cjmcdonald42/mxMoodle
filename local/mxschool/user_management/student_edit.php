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
 * Student edit page for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/events/page_visited.php');
require_once('student_edit_form.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_students', context_system::instance());

$id = required_param('id', PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('user_management', 'local_mxschool') => '/local/mxschool/user_management/index.php',
    get_string('student_report', 'local_mxschool') => '/local/mxschool/user_management/student_report.php'
);
$url = '/local/mxschool/user_management/student_edit.php';
$title = get_string('student_edit', 'local_mxschool');
$dorms = get_dorms_list();
$advisors = get_advisor_list();

if (!$DB->record_exists('local_mxschool_student', array('id' => $id))) {
    redirect(new moodle_url(end(array_values($parents))));
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

$data = $DB->get_record_sql(
    "SELECT s.id, s.userid, s.permissionsid,
            u.firstname, u.middlename, u.lastname, u.alternatename, u.email,
            s.admission_year AS admissionyear, s.grade, s.gender, s.advisorid AS advisor,
            s.boarding_status AS isboarder, s.boarding_status_next_year AS isboardernextyear, s.dormid AS dorm, s.room, s.phone_number AS phonenumber, s.birthday,
            p.overnight, p.may_ride_with AS riding, p.ride_permission_details AS comment, p.ride_share AS rideshare,
            p.may_drive_to_boston AS boston, p.may_drive_to_town AS town, p.may_drive_passengers AS passengers,
            p.swim_competent AS swimcompetent, p.swim_allowed AS swimallowed, p.boat_allowed AS boatallowed
    FROM {local_mxschool_student} s
    LEFT JOIN {user} u ON s.userid = u.id
    LEFT JOIN {local_mxschool_permissions} p ON s.permissionsid = p.id
    WHERE s.id = ?", array($id)
);

$form = new student_edit_form(null, array('id' => $id, 'dorms' => $dorms, 'advisors' => $advisors));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect(new moodle_url(end(array_values($parents))));
} else if ($data = $form->get_data()) {
    $user = new stdClass();
    $user->id = $data->userid;
    $user->firstname = $data->firstname;
    $user->middlename = $data->middlename;
    $user->lastname = $data->lastname;
    $user->alternatename = $data->alternatename;
    $user->email = $data->email;
    $DB->update_record('user', $user);

    $student = new stdClass();
    $student->id = $data->id;
    $student->admission_year = $data->admissionyear;
    $student->grade = $data->grade;
    $student->gender = $data->gender;
    $student->advisorid = $data->advisor;
    $student->boarding_status = $data->isboarder;
    $student->boarding_status_next_year = $data->isboardernextyear;
    $student->dormid = $data->dorm;
    $student->room = $data->room ?: null;
    $student->phone_number = $data->phonenumber;
    $student->birthday = $data->birthday;
    $DB->update_record('local_mxschool_student', $student);

    $permissions = new stdClass();
    $permissions->id = $data->permissionsid;
    $permissions->overnight = $data->overnight;
    $permissions->may_ride_with = $data->riding;
    $permissions->ride_permission_details = $data->comment;
    $permissions->ride_share = $data->rideshare;
    $permissions->may_drive_to_boston = $data->boston;
    $permissions->may_drive_to_town = $data->town;
    $permissions->may_drive_passengers = $data->passengers;
    $permissions->swim_competent = $data->swimcompetent;
    $permissions->swim_allowed = $data->swimallowed;
    $permissions->boat_allowed = $data->boatallowed;
    $DB->update_record('local_mxschool_permissions', $permissions);

    redirect(new moodle_url(end(array_values($parents))), get_string('student_edit_success', 'local_mxschool'), null, \core\output\notification::NOTIFY_SUCCESS);
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form_page($form);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
