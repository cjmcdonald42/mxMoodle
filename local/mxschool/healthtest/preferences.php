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
 * Preferances Panel for Middlesex's Health Test system.
 *
 * @package     local_mxschool
 * @subpackage  healthtest
 * @author      Aarav Mehta, Class of 2023 <amehta@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_healthtest_preferences', context_system::instance());

setup_mxschool_page('preferences', 'healthtest');

$data = new stdClass();

// Set form fields to their current values
$data->healthtest_enabled = get_config('local_mxschool', 'healthtest_enabled');
$data->form_instructions = get_config('local_mxschool', 'healthtest_form_instructions');
// $data->reminder_enabled = get_config('local_mxschool', 'healthtest_reminder_enabled');
$data->missed_copy_healthcenter_enabled = get_config('local_mxschool', 'healthtest_copy_healthcenter');
$data->healthtest_notification_email_address = get_config('local_mxschool', 'healthtest_notification_email_address');
$data->confirm_enabled = get_config('local_mxschool', 'healthtest_confirm_enabled');
generate_email_preference_fields('healthtest_reminder', $data, 'reminder');
generate_email_preference_fields('healthtest_missed', $data, 'missed');
generate_email_preference_fields('healthtest_confirm', $data, 'confirm');

// Create form
$form = new local_mxschool\local\healthtest\preferences_form();
$form->set_data($data);

if ($form->is_cancelled()) { // If the cancel button is pressed...
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
	if(!isset($data->healthtest_enabled)) $data->healthtest_enabled = '0';
	if(!isset($data->missed_copy_healthcenter_enabled)) $data->missed_copy_healthcenter_enabled = '0';
	if(!isset($data->confirm_enabled)) $data->confirm_enabled = '0';
	// Set configs according to preferences form data
	set_config('healthtest_enabled', $data->healthtest_enabled, 'local_mxschool');
	set_config('healthtest_form_instructions', $data->form_instructions, 'local_mxschool');
	// set_config('healthtest_reminder_enabled', $data->reminder_enabled, 'local_mxschool');
	set_config('healthtest_copy_healthcenter', $data->missed_copy_healthcenter_enabled, 'local_mxschool');
    set_config('healthtest_notification_email_address', $data->healthtest_notification_email_address, 'local_mxschool');
	set_config('healthtest_confirm_enabled', $data->confirm_enabled, 'local_mxschool');

    update_notification('healthtest_reminder', $data, 'reminder');
	update_notification('healthtest_missed', $data, 'missed');
    update_notification('healthtest_confirm', $data, 'confirm');

    logged_redirect($form->get_redirect(), get_string('healthtest:preferences:success', 'local_mxschool'), 'update');
}

// Output form onto page
$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
