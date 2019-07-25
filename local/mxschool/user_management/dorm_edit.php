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
 * Dorm edit page for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  user_management
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_dorms', context_system::instance());

$id = optional_param('id', 0, PARAM_INT);

setup_edit_page('dorm_edit', 'dorm_report', 'user_management');

$queryfields = array('local_mxschool_dorm' => array('abbreviation' => 'd', 'fields' => array(
    'id', 'hohid' => 'hoh', 'name', 'abbreviation', 'type', 'gender', 'available', 'permissions_line' => 'permissionsline'
)));

if ($id) { // Updating an existing record.
    if (!$DB->record_exists('local_mxschool_dorm', array('id' => $id, 'deleted' => 0))) {
        redirect_to_fallback();
    }
    $data = get_record($queryfields, 'd.id = ?', array($id));
} else { // Creating a new record.
    $data = new stdClass();
    $data->id = $id;
}
$faculty = get_faculty_list();

$form = new local_mxschool\local\user_management\dorm_edit_form(array('faculty' => $faculty));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    update_record($queryfields, $data);
    logged_redirect(
        $form->get_redirect(),
        get_string($data->id ? 'user_management_dorm_edit_success' : 'user_management_dorm_create_success', 'local_mxschool'),
        $data->id ? 'update' : 'create'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
