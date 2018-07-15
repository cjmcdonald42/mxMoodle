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
 * Page for peer tutors to submit tutoring records for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/../mxschool/locallib.php');
require_once(__DIR__.'/../mxschool/classes/output/renderable.php');
require_once('locallib.php');
require_once('tutoring_form.php');

require_login();
$istutor = user_is_tutor();
if ($istutor) {
    require_capability('local/peertutoring:enter_tutoring', context_system::instance());
} else {
    require_capability('local/peertutoring:manage_tutoring', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('pluginname', 'local_peertutoring') => '/local/peertutoring/index.php'
);
$redirect = new moodle_url($parents[array_keys($parents)[count($parents) - 1]]);
$url = '/local/peertutoring/tutoring_enter.php';
$title = get_string('tutoring_form', 'local_peertutoring');

setup_mxschool_page($url, $title, $parents);

$queryfields = array('local_peertutoring_session' => array('abbreviation' => 's', 'fields' => array(
    'id', 'tutorid' => 'tutor', 'tutoring_date' => 'tutoringdate', 'studentid' => 'student', 'courseid' => 'course', 'topic',
    'typeid' => 'type_select', 'other' => 'type_other', 'ratingid' => 'rating', 'notes', 'time_created' => 'timecreated',
    'time_modified' => 'timemodified'
)));

if ($id) {
    if (!$DB->record_exists('local_peertutoring_session', array('id' => $id))) {
        redirect($redirect);
    }
    if ($istutor) { // Tutors cannot edit existing tutoring records.
        redirect(new moodle_url($url));
    }
    $data = get_record($queryfields, "s.id = ?", array($id));
    $data->department = $DB->get_field('local_peertutoring_course', 'departmentid', array('id' => $data->course));
} else {
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = time();
    if ($istutor) {
        $data->tutor = $USER->id;
        $record = $DB->get_record_sql(
            "SELECT CONCAT(u.firstname, ' ', u.lastname) AS tutor FROM {user} u WHERE u.id = ?", array($USER->id)
        );
    }
}
$data->istutor = $istutor ? '1' : '0';
$tutors = get_tutor_list();
$students = get_student_list();
$departments = array(0 => get_string('tutoring_form_department_default', 'local_peertutoring')) + get_department_list();
$courses = array(0 => get_string('tutoring_form_course_default', 'local_peertutoring')) + get_course_list();
$types = array(0 => get_string('tutoring_form_type_default', 'local_peertutoring')) + get_type_list();
$ratings = array(0 => get_string('tutoring_form_rating_default', 'local_peertutoring')) + get_rating_list();

$form = new tutoring_form(array(
    'id' => $id, 'tutors' => $tutors, 'students' => $students, 'departments' => $departments, 'courses' => $courses,
    'types' => $types, 'ratings' => $ratings
));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->timemodified = time();
    if ($data->type_select !== '5') {
        unset($data->type_other);
    }
    update_record($queryfields, $data);
    redirect(
        $form->get_redirect(), get_string('tutoring_form_success', 'local_peertutoring'), null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$formrenderable = new \local_mxschool\output\form($form);
$jsrenderable1 = new \local_mxschool\output\amd_module('local_peertutoring/get_tutor_options');
$jsrenderable2 = new \local_mxschool\output\amd_module('local_peertutoring/get_department_courses');

echo $output->header();
echo $output->heading($title.($istutor ? " for {$record->tutor}" : ''));
echo $output->render($formrenderable);
echo $output->render($jsrenderable1);
echo $output->render($jsrenderable2);
echo $output->footer();
