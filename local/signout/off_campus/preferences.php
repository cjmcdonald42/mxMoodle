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
 * Off-campus preferences page for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  off_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/signout:manage_off_campus_preferences', context_system::instance());

$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('preferences', 'off_campus', 'signout');

if ($action === 'delete' && $id) {
    $result = $DB->record_exists('local_signout_type', array('id' => $id)) ? 'success' : 'failure';
    $DB->set_field('local_signout_type', 'deleted', 1, array('id' => $id));
    logged_redirect(
        new moodle_url($PAGE->url, (array) $filter), get_string("off_campus:type:delete:{$result}", 'local_signout'), 'delete',
        $result === 'success'
    );
}

$data = new stdClass();
$data->edit_window = get_config('local_signout', 'off_campus_edit_window');
$data->trip_window = get_config('local_signout', 'off_campus_trip_window');
$data->enabled = get_config('local_signout', 'off_campus_form_enabled');
$data->permissions_active = get_config('local_signout', 'off_campus_form_permissions_active');
$data->ip_enabled = get_config('local_signout', 'off_campus_ipvalidation_enabled');
generate_email_preference_fields('off_campus_submitted', $data);
$data->ip_form_error['text'] = get_config('local_signout', 'off_campus_form_ipvalidation_error');
$data->ip_sign_in_error['text'] = get_config('local_signout', 'off_campus_signin_ipvalidation_error');
$data->passenger_instructions['text'] = get_config('local_signout', 'off_campus_form_instructions_passenger');
$data->bottom_instructions['text'] = get_config('local_signout', 'off_campus_form_instructions_bottom');
$data->confirmation['text'] = get_config('local_signout', 'off_campus_form_confirmation');
$data->form_driver_no_passengers['text'] = get_config('local_signout', 'off_campus_form_warning_driver_nopassengers');
$data->form_passenger_parent['text'] = get_config('local_signout', 'off_campus_form_warning_passenger_parent');
$data->form_passenger_specific['text'] = get_config('local_signout', 'off_campus_form_warning_passenger_specific');
$data->form_passenger_over_21['text'] = get_config('local_signout', 'off_campus_form_warning_passenger_over21');
$data->form_rideshare_parent['text'] = get_config('local_signout', 'off_campus_form_warning_rideshare_parent');
$data->form_rideshare_not_allowed['text'] = get_config('local_signout', 'off_campus_form_warning_rideshare_notallowed');
$data->email_driver_no_passengers['text'] = get_config('local_signout', 'off_campus_notification_warning_driver_nopassengers');
$data->email_passenger_parent['text'] = get_config('local_signout', 'off_campus_notification_warning_passenger_parent');
$data->email_passenger_specific['text'] = get_config('local_signout', 'off_campus_notification_warning_passenger_specific');
$data->email_passenger_over_21['text'] = get_config('local_signout', 'off_campus_notification_warning_passenger_over21');
$data->email_rideshare_parent['text'] = get_config('local_signout', 'off_campus_notification_warning_rideshare_parent');
$data->email_rideshare_not_allowed['text'] = get_config('local_signout', 'off_campus_notification_warning_rideshare_notallowed');
$data->irregular['text'] = get_config('local_signout', 'off_campus_notification_warning_irregular');

$form = new local_signout\local\off_campus\preferences_form();
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    set_config('off_campus_edit_window', $data->edit_window, 'local_signout');
    set_config('off_campus_trip_window', $data->trip_window, 'local_signout');
    set_config('off_campus_form_enabled', $data->enabled, 'local_signout');
    set_config('off_campus_form_permissions_active', $data->permissions_active, 'local_signout');
    set_config('off_campus_ipvalidation_enabled', $data->ip_enabled, 'local_signout');
    update_notification('off_campus_submitted', $data);
    set_config('off_campus_form_ipvalidation_error', $data->ip_form_error['text'], 'local_signout');
    set_config('off_campus_signin_ipvalidation_error', $data->ip_sign_in_error['text'], 'local_signout');
    set_config('off_campus_form_instructions_passenger', $data->passenger_instructions['text'], 'local_signout');
    set_config('off_campus_form_instructions_bottom', $data->bottom_instructions['text'], 'local_signout');
    set_config('off_campus_form_confirmation', $data->confirmation['text'], 'local_signout');
    set_config('off_campus_form_warning_driver_nopassengers', $data->form_driver_no_passengers['text'], 'local_signout');
    set_config('off_campus_form_warning_passenger_parent', $data->form_passenger_parent['text'], 'local_signout');
    set_config('off_campus_form_warning_passenger_specific', $data->form_passenger_specific['text'], 'local_signout');
    set_config('off_campus_form_warning_passenger_over21', $data->form_passenger_over_21['text'], 'local_signout');
    set_config('off_campus_form_warning_rideshare_parent', $data->form_rideshare_parent['text'], 'local_signout');
    set_config('off_campus_form_warning_rideshare_notallowed', $data->form_rideshare_not_allowed['text'], 'local_signout');
    set_config('off_campus_notification_warning_driver_nopassengers', $data->email_driver_no_passengers['text'], 'local_signout');
    set_config('off_campus_notification_warning_passenger_parent', $data->email_passenger_parent['text'], 'local_signout');
    set_config('off_campus_notification_warning_passenger_specific', $data->email_passenger_specific['text'], 'local_signout');
    set_config('off_campus_notification_warning_passenger_over21', $data->email_passenger_over_21['text'], 'local_signout');
    set_config('off_campus_notification_warning_rideshare_parent', $data->email_rideshare_parent['text'], 'local_signout');
    set_config('off_campus_notification_warning_rideshare_notallowed', $data->email_rideshare_not_allowed['text'], 'local_signout');
    set_config('off_campus_notification_warning_irregular', $data->irregular['text'], 'local_signout');
    logged_redirect($form->get_redirect(), get_string('off_campus:preferences:update:success', 'local_signout'), 'update');
}

$table = new local_signout\local\off_campus\type_table();
$buttons = array(new local_mxschool\output\redirect_button(
    get_string('off_campus:type_report:add', 'local_signout'), new moodle_url('/local/signout/off_campus/type_edit.php')
));

$output = $PAGE->get_renderer('local_signout');
$renderable = new local_mxschool\output\form($form);
$reportrenderable = new local_mxschool\output\report($table, null, array(), $buttons);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->heading(get_string('off_campus:type_report', 'local_signout'));
echo $output->render($reportrenderable);
echo $output->footer();
