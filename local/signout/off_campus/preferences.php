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
 * @package    local_signout
 * @subpackage off_campus
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../../mxschool/locallib.php');
require_once(__DIR__.'/../../mxschool/classes/output/renderable.php');
require_once(__DIR__.'/preferences_form.php');

require_login();
require_capability('local/signout:manage_off_campus_preferences', context_system::instance());

setup_mxschool_page('preferences', 'off_campus', 'signout');

$data = new stdClass();
$data->editwindow = get_config('local_signout', 'off_campus_edit_window');
$data->tripwindow = get_config('local_signout', 'off_campus_trip_window');
$data->offcampusenabled = get_config('local_signout', 'off_campus_form_enabled');
$data->ipenabled = get_config('local_signout', 'off_campus_form_ipenabled');
$notification = get_notification('off_campus_submitted');
$data->subject = $notification->subject;
$data->body['text'] = $notification->body_html;
$data->ipformerror['text'] = get_config('local_signout', 'off_campus_form_iperror');
$data->passengerinstructions['text'] = get_config('local_signout', 'off_campus_form_instructions_passenger');
$data->bottominstructions['text'] = get_config('local_signout', 'off_campus_form_instructions_bottom');
$data->nopassengers['text'] = get_config('local_signout', 'off_campus_form_warning_nopassengers');
$data->needparent['text'] = get_config('local_signout', 'off_campus_form_warning_needparent');
$data->onlyspecific['text'] = get_config('local_signout', 'off_campus_form_warning_onlyspecific');
$data->confirmation['text'] = get_config('local_signout', 'off_campus_form_confirmation');
$data->irregular['text'] = get_config('local_signout', 'off_campus_notification_warning_irregular');
$data->driver['text'] = get_config('local_signout', 'off_campus_notification_warning_driver');
$data->any['text'] = get_config('local_signout', 'off_campus_notification_warning_any');
$data->parent['text'] = get_config('local_signout', 'off_campus_notification_warning_parent');
$data->specific['text'] = get_config('local_signout', 'off_campus_notification_warning_specific');
$data->over21['text'] = get_config('local_signout', 'off_campus_notification_warning_over21');
$data->unsetpermissions['text'] = get_config('local_signout', 'off_campus_notification_warning_unsetpermissions');

$form = new preferences_form();
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    set_config('off_campus_edit_window', $data->editwindow, 'local_signout');
    set_config('off_campus_trip_window', $data->tripwindow, 'local_signout');
    set_config('off_campus_form_enabled', $data->offcampusenabled, 'local_signout');
    set_config('off_campus_form_ipenabled', $data->ipenabled, 'local_signout');
    update_notification('off_campus_submitted', $data->subject, $data->body);
    set_config('off_campus_form_iperror', $data->ipformerror['text'], 'local_signout');
    set_config('off_campus_form_instructions_passenger', $data->passengerinstructions['text'], 'local_signout');
    set_config('off_campus_form_instructions_bottom', $data->bottominstructions['text'], 'local_signout');
    set_config('off_campus_form_warning_nopassengers', $data->nopassengers['text'], 'local_signout');
    set_config('off_campus_form_warning_needparent', $data->needparent['text'], 'local_signout');
    set_config('off_campus_form_warning_onlyspecific', $data->onlyspecific['text'], 'local_signout');
    set_config('off_campus_form_confirmation', $data->confirmation['text'], 'local_signout');
    set_config('off_campus_notification_warning_irregular', $data->irregular['text'], 'local_signout');
    set_config('off_campus_notification_warning_driver', $data->driver['text'], 'local_signout');
    set_config('off_campus_notification_warning_any', $data->any['text'], 'local_signout');
    set_config('off_campus_notification_warning_parent', $data->parent['text'], 'local_signout');
    set_config('off_campus_notification_warning_specific', $data->specific['text'], 'local_signout');
    set_config('off_campus_notification_warning_over21', $data->over21['text'], 'local_signout');
    set_config('off_campus_notification_warning_unsetpermissions', $data->unsetpermissions['text'], 'local_signout');
    logged_redirect(
        $form->get_redirect(), get_string('off_campus_preferences_edit_success', 'local_signout'), 'update'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
