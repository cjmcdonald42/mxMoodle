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
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/../mxschool/locallib.php');
require_once(__DIR__.'/../mxschool/classes/output/renderable.php');
require_once(__DIR__.'/locallib.php');
require_once(__DIR__.'/tutoring_form.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/peertutoring:manage_tutoring', context_system::instance());
}

$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('tutoring_form', null, 'peertutoring');
$redirect = get_redirect();

$queryfields = array('local_peertutoring_session' => array('abbreviation' => 's', 'fields' => array(
    'id', 'tutorid' => 'tutor', 'tutoring_date' => 'tutoringdate', 'studentid' => 'student', 'courseid' => 'course', 'topic',
    'typeid' => 'type_select', 'other' => 'type_other', 'ratingid' => 'rating', 'notes', 'time_created' => 'timecreated',
    'time_modified' => 'timemodified'
)));

if ($isstudent && !student_may_access_tutoring($USER->id)) {
    redirect($redirect);
}
if ($id) {
    if (!$DB->record_exists('local_peertutoring_session', array('id' => $id))) {
        redirect($redirect);
    }
    if ($isstudent) { // Students cannot edit existing tutoring records.
        redirect($PAGE->url);
    }
    $data = get_record($queryfields, "s.id = ?", array($id));
    $data->department = $DB->get_field('local_peertutoring_course', 'departmentid', array('id' => $data->course));
} else {
    $data = new stdClass();
    $data->id = $id;
    $data->timecreated = time();
    if ($isstudent) {
        $data->tutor = $USER->id;
    }
}
$data->isstudent = $isstudent ? '1' : '0';
$tutors = get_tutor_list();
$students = get_student_list();
$departments = array(0 => get_string('form_select_default', 'local_mxschool')) + get_department_list();
$courses = array(0 => get_string('form_select_default', 'local_mxschool')) + get_course_list();
$types = array(0 => get_string('form_select_default', 'local_mxschool')) + get_type_list()
         + array(-1 => get_string('tutoring_form_type_select_other', 'local_peertutoring'));
$ratings = array(0 => get_string('form_select_default', 'local_mxschool')) + get_rating_list();

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
    if ($data->type_select !== '-1') {
        $data->type_other = null;
    }
    update_record($queryfields, $data);
    logged_redirect(
        $form->get_redirect(), get_string('tutoring_form_success', 'local_peertutoring'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$formrenderable = new \local_mxschool\output\form($form);
$jsrenderable = new \local_mxschool\output\amd_module('local_peertutoring/tutoring_form');

echo $output->header();
echo $output->heading(
    $isstudent ? get_string('tutoring_form_title', 'local_peertutoring', format_student_name($USER->id)) : $PAGE->title
);
echo $output->render($formrenderable);
echo $output->render($jsrenderable);
echo $output->footer();
