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
 * Edit page for tutor records for Middlesex School's Peer Tutoring Subplugin.
 *
 * @package    local_peertutoring
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/../mxschool/locallib.php');
require_once(__DIR__.'/../mxschool/classes/output/renderable.php');
require_once('locallib.php');
require_once('tutor_edit_form.php');

require_login();
require_capability('local/peertutoring:manage_preferences', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('pluginname', 'local_peertutoring') => '/local/peertutoring/index.php',
    get_string('preferences', 'local_peertutoring') => '/local/peertutoring/preferences.php',
);
$redirect = get_redirect($parents);
$url = '/local/peertutoring/tutor_edit.php';
$title = get_string('tutor_edit', 'local_peertutoring');

setup_mxschool_page($url, $title, $parents);

$queryfields = array('local_peertutoring_tutor' => array('abbreviation' => 't', 'fields' => array(
    'id', 'userid' => 'student', 'departments'
)));

if ($id && !$DB->record_exists('local_peertutoring_tutor', array('id' => $id))) {
    redirect($redirect);
}

$data = get_record($queryfields, "t.id = ?", array($id));
if ($data) {
    $data->departments = json_decode($data->departments);
} else {
    $data = new stdClass();
    $data->id = $id;
}

$students = get_eligible_student_list();
$departments = get_department_list();

$form = new tutor_edit_form(array('id' => $id, 'students' => $students, 'departments' => $departments));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $data->departments = json_encode($data->departments);
    update_record($queryfields, $data);
    logged_redirect(
        $form->get_redirect(), $data->id ? get_string('tutor_edit_success', 'local_peertutoring')
        : get_string('tutor_create_success', 'local_peertutoring'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form($form);
$jsrenderable = new \local_mxschool\output\amd_module('local_peertutoring/tutor_form');

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->render($jsrenderable);
echo $output->footer();
