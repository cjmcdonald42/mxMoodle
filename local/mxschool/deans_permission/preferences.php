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
 * deans permission preferences page for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  deans_permission
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_rooming_preferences', context_system::instance());

$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('preferences', 'deans_permission');

$redirect = new moodle_url($PAGE->url, (array) $filter);
if ($action === 'delete' && $id) {
    $result = $DB->record_exists('local_mxschool_dp_event', array('id' => $id)) ? 'success' : 'failure';
    $DB->delete_records('local_mxschool_dp_event', array('id' => $id));
    logged_redirect(
        $redirect, get_string("deans_permission:report:delete:{$result}", 'local_mxschool'), 'delete', $result === 'success'
    );
}

$data = new stdClass();
$data->deans_email_address = get_config('local_mxschool', 'deans_email_address');
generate_email_preference_fields('deans_permission_submitted', $data, 'submitted');
$data->sports_email_address = get_config('local_mxschool', 'athletic_director_email_address');
generate_email_preference_fields('sports_permission_request', $data, 'sports');
$data->class_email_address = get_config('local_mxschool', 'academic_director_email_address');
generate_email_preference_fields('class_permission_request', $data, 'class');
generate_email_preference_fields('deans_permission_approved', $data, 'approved');

$form = new local_mxschool\local\deans_permission\preferences_form();
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
	set_config('deans_email_address', $data->deans_email_address, 'local_mxschool');
	set_config('athletic_director_email_address', $data->sports_email_address, 'local_mxschool');
	set_config('academic_director_email_address', $data->class_email_address, 'local_mxschool');
	update_notification('deans_permission_submitted', $data, 'submitted');
	update_notification('sports_permission_request', $data, 'sports');
	update_notification('class_permission_request', $data, 'class');
	update_notification('deans_permission_approved', $data, 'approved');
	logged_redirect($form->get_redirect(), get_string('deans_permission:preferences:update:success', 'local_mxschool'), 'update');
}

$table = new local_mxschool\local\deans_permission\event_table();
$buttons = array(new local_mxschool\output\redirect_button(
    get_string('deans_permission:preferences:add_event', 'local_mxschool'), new moodle_url('/local/mxschool/deans_permission/event_edit.php')
));

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);
$reportrenderable = new local_mxschool\output\report($table, null, array(), $buttons);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->heading(get_string('deans_permission:event_report:title', 'local_mxschool'));
echo $output->render($reportrenderable);
echo $output->footer();
