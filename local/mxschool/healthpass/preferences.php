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
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
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
$data->reset_time = get_config('local_mxschool', 'healthpass_reset_time');
$data->client_id = get_config('local_mxschool', 'client_id');
$data->client_secret = get_config('local_mxschool', 'client_secret');
$data->app_id = get_config('local_mxschool', 'app_id');
$data->app_token = get_config('local_mxschool', 'app_token');

// Create form
$form = new local_mxschool\local\healthpass\preferences_form();
$form->set_data($data);

if ($form->is_cancelled()) { // If the cancel button is pressed...
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
	// Set configs according to preferences form data
	set_config('healthpass_enabled', $data->healthpass_enabled, 'local_mxschool');
	set_config('healthpass_reset_time', generate_timestamp($data, 'reset_time'), 'local_mxschool');
	set_config('client_id', $data->client_id, 'local_mxschool');
	set_config('client_secret', $data->client_secret, 'local_mxschool');
	set_config('app_id', $data->app_id, 'local_mxschool');
	set_config('app_token', $data->app_token, 'local_mxschool');
     logged_redirect($form->get_redirect(), get_string('healthpass:preferences:success', 'local_mxschool'), 'update');
}

// Output form onto page
$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
