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
 * Parent edit page for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  user_management
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_students', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

setup_edit_page('parent_edit', 'student_report', 'user_management');

$queryfields = array(
    'local_mxschool_parent' => array(
        'abbreviation' => 'p',
        'fields' => array(
            'id', 'userid' => 'student', 'parent_name' => 'name', 'is_primary_parent' => 'is_primary', 'relationship', 'home_phone',
            'cell_phone', 'work_phone', 'email'
        )
    )
);

if ($id) { // Updating an existing record.
    if (!$DB->record_exists('local_mxschool_parent', array('id' => $id, 'deleted' => 0))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, 'p.id = ?', array($id));
} else { // Creating a new record.
    $data = new stdClass();
    $data->id = $id;
    $data->is_primary = '-1'; // Invalid default to prevent auto selection.
}
$students = get_student_list();

$form = new local_mxschool\local\user_management\parent_edit_form(array('students' => $students));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    update_record($queryfields, $data);
    $action = $data->id ? 'update' : 'create';
    logged_redirect($form->get_redirect(), get_string("user_management:parent:{$action}:success", 'local_mxschool'), $action);
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
