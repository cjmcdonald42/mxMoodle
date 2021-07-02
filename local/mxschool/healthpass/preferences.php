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
 * Preferances Panel for Middlesex's Health Pass system.
 *
 * @package     local_mxschool
 * @subpackage  healthpass
 * @author      Aarav Mehta, Class of 2023 <amehta@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_healthpass_preferences', context_system::instance());

setup_mxschool_page('preferences', 'healthpass');

$data = new stdClass();

// Set form fields to their current values
$data->healthpass_enabled = get_config('local_mxschool', 'healthpass_enabled');
$data->one_per_day = get_config('local_mxschool', 'healthpass_one_per_day');

$data->max_body_temp = get_config('local_mxschool', 'healthpass_max_body_temp');
$data->healthcenter_notification_enabled = get_config('local_mxschool', 'healthcenter_notification_enabled');
$data->healthpass_notification_email_address = get_config('local_mxschool', 'healthpass_notification_email_address');
$data->days_before_reminder = get_config('local_mxschool', 'healthpass_days_before_reminder');
generate_email_preference_fields('healthcenter_notification', $data, 'healthcenter');
generate_email_preference_fields('healthpass_approved', $data, 'approved');
generate_email_preference_fields('healthpass_denied', $data, 'denied');
generate_email_preference_fields('healthpass_overridden', $data, 'overridden');
generate_email_preference_fields('healthpass_notify_unsubmitted', $data, 'unsubmitted');

// Create form
$form = new local_mxschool\local\healthpass\preferences_form();
$form->set_data($data);

if ($form->is_cancelled()) { // If the cancel button is pressed...
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
	if(!isset($data->healthpass_enabled)) $data->healthpass_enabled = '0';
	if(!isset($data->healthcenter_notification_enabled)) $data->healthcenter_notification_enabled = '0';
	if(!isset($data->one_per_day)) $data->one_per_day = '0';
	// Set configs according to preferences form data
	set_config('healthpass_enabled', $data->healthpass_enabled, 'local_mxschool');
	set_config('healthpass_max_body_temp', $data->max_body_temp, 'local_mxschool');
	set_config('healthcenter_notification_enabled', $data->healthcenter_notification_enabled, 'local_mxschool');
	set_config('healthpass_notification_email_address', $data->healthpass_notification_email_address, 'local_mxschool');
	set_config('healthpass_days_before_reminder', $data->days_before_reminder, 'local_mxschool');
	set_config('healthpass_one_per_day', $data->one_per_day, 'local_mxschool');
     update_notification('healthcenter_notification', $data, 'healthcenter');
	update_notification('healthpass_approved', $data, 'approved');
     update_notification('healthpass_denied', $data, 'denied');
	update_notification('healthpass_overridden', $data, 'overridden');
	update_notification('healthpass_notify_unsubmitted', $data, 'unsubmitted');
     logged_redirect($form->get_redirect(), get_string('healthpass:preferences:success', 'local_mxschool'), 'update');
}

// Output form onto page
$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
