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
 * Parent edit page for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require(__DIR__.'/../../../config.php');
 require_once('parent_edit_form.php');
 require_once(__DIR__.'/../classes/output/renderable.php');
 require_once(__DIR__.'/../classes/events/page_visited.php');
 require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_students', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('user_management', 'local_mxschool') => '/local/mxschool/user_management/index.php',
    get_string('student_report', 'local_mxschool') => '/local/mxschool/user_management/student_report.php'
);
$redirect = new moodle_url($parents[array_keys($parents)[count($parents) - 1]]);
$url = '/local/mxschool/user_management/parent_edit.php';
$title = get_string('parent_edit', 'local_mxschool');
$queryfields = array('local_mxschool_parent' => array('abbreviation' => 'p', 'fields' => array(
    'id', 'userid' => 'student', 'parent_name' => 'name', 'is_primary_parent' => 'isprimary', 'relationship',
    'home_phone' => 'homephone', 'cell_phone' => 'cellphone', 'work_phone' => 'workphone', 'email'
)));
$students = get_student_list();

if ($id && !$DB->record_exists('local_mxschool_parent', array('id' => $id))) {
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

$form = new parent_edit_form(null, array('id' => $id, 'students' => $students));
$form->set_redirect($redirect);
$data = get_record($queryfields, "p.id = ?", array($id));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    $oldrecord = $DB->get_record('local_mxschool_parent', array('id' => $data->id), 'is_primary_parent');
    if ($oldrecord->is_primary_parent === 'Yes' && $data->isprimary === 'No') {
        // Each student must have a primary parent.
        $newprimary = $DB->get_record_sql(
            "SELECT id, is_primary_parent FROM {local_mxschool_parent} WHERE userid = ? AND id != ? AND deleted = 0",
            array($data->student, $data->id), IGNORE_MULTIPLE
        );
        if ($newprimary) {
            $newprimary->is_primary_parent = 'Yes';
            $DB->update_record('local_mxschool_parent', $newprimary);
        }
    } else if ($oldrecord->is_primary_parent === 'No' && $data->isprimary === 'Yes') {
        // Each student can only have one primary parent.
        $oldprimary = $DB->get_record_sql(
            "SELECT id, is_primary_parent FROM {local_mxschool_parent}
             WHERE userid = ? AND is_primary_parent = 'Yes' AND deleted = 0",
            array($data->student)
        );
        if ($oldprimary) {
            $oldprimary->is_primary_parent = 'No';
            $DB->update_record('local_mxschool_parent', $oldprimary);
        }
    }
    update_record($queryfields, $data);
    redirect(
        $form->get_redirect(), get_string('parent_edit_success', 'local_mxschool'), null, \core\output\notification::NOTIFY_SUCCESS
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form_page($form);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
