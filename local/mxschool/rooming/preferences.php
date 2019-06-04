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
 * Rooming preferences page for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage rooming
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once('preferences_form.php');

require_login();
require_capability('local/mxschool:manage_rooming_preferences', context_system::instance());

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('rooming', 'local_mxschool') => '/local/mxschool/rooming/index.php'
);
$redirect = get_redirect($parents);
$url = '/local/mxschool/rooming/preferences.php';
$title = get_string('rooming_preferences', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$data = new stdClass();
$data->start_date = get_config('local_mxschool', 'rooming_form_start_date') ?: get_config('local_mxschool', 'dorms_open_date');
generate_time_selector_fields($data, 'start');
$data->stop_date = get_config('local_mxschool', 'rooming_form_stop_date') ?: get_config('local_mxschool', 'dorms_close_date');
generate_time_selector_fields($data, 'stop');
$submittednotification = get_notification('rooming_submitted');
$data->submitted_subject = $submittednotification->subject;
$data->submitted_body['text'] = $submittednotification->body_html;
$unsubmittednotification = get_notification('rooming_notify_unsubmitted');
$data->unsubmitted_subject = $unsubmittednotification->subject;
$data->unsubmitted_body['text'] = $unsubmittednotification->body_html;
$data->roommateinstructions['text'] = get_config('local_mxschool', 'rooming_form_roommate_instructions');

$form = new preferences_form();
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    set_config('rooming_form_start_date', generate_timestamp($data, 'start'), 'local_mxschool');
    set_config('rooming_form_stop_date', generate_timestamp($data, 'stop'), 'local_mxschool');
    update_notification('rooming_submitted', $data->submitted_subject, $data->submitted_body);
    update_notification('rooming_notify_unsubmitted', $data->unsubmitted_subject, $data->unsubmitted_body);
    set_config('rooming_form_roommate_instructions', $data->roommateinstructions['text'], 'local_mxschool');
    logged_redirect(
        $form->get_redirect(), get_string('rooming_preferences_edit_success', 'local_mxschool'), 'update'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
