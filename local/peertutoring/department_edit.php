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
 * Edit page for department records for Middlesex School's Peer Tutoring Subplugin.
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
require_once('department_edit_form.php');

require_login();
require_capability('local/peertutoring:manage_preferences', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('pluginname', 'local_peertutoring') => '/local/peertutoring/index.php',
    get_string('preferences', 'local_peertutoring') => '/local/peertutoring/preferences.php',
);
$redirect = get_redirect($parents);
$url = '/local/peertutoring/department_edit.php';
$title = get_string('department_edit', 'local_peertutoring');

setup_mxschool_page($url, $title, $parents);

$queryfields = array('local_peertutoring_dept' => array('abbreviation' => 'd', 'fields' => array('id', 'name')));

if ($id && !$DB->record_exists('local_peertutoring_dept', array('id' => $id))) {
    redirect($redirect);
}

$data = get_record($queryfields, "d.id = ?", array($id));

$form = new department_edit_form(array('id' => $id));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    update_record($queryfields, $data);
    logged_redirect(
        $form->get_redirect(), $data->id ? get_string('department_edit_success', 'local_peertutoring')
        : get_string('department_create_success', 'local_peertutoring'), $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
