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
 * Advisor selection preferences page for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  advisor_selection
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_advisor_selection_preferences', context_system::instance());

setup_mxschool_page('preferences', 'advisor_selection');

$data = new stdClass();
$data->start_date = get_config('local_mxschool', 'advisor_form_start_date') ?: get_config('local_mxschool', 'dorms_open_date');
generate_time_selector_fields($data, 'start');
$data->stop_date = get_config('local_mxschool', 'advisor_form_stop_date') ?: get_config('local_mxschool', 'dorms_close_date');
generate_time_selector_fields($data, 'stop');
$data->who = get_config('local_mxschool', 'advisor_form_enabled_who');
$submittednotification = get_notification('advisor_selection_submitted');
$data->submitted_subject = $submittednotification->subject;
$data->submitted_body['text'] = $submittednotification->body_html;
$unsubmittednotification = get_notification('advisor_selection_notify_unsubmitted');
$data->unsubmitted_subject = $unsubmittednotification->subject;
$data->unsubmitted_body['text'] = $unsubmittednotification->body_html;
$resultsnotification = get_notification('advisor_selection_notify_results');
$data->results_subject = $resultsnotification->subject;
$data->results_body['text'] = $resultsnotification->body_html;
$data->closing_warning['text'] = get_config('local_mxschool', 'advisor_form_closing_warning');
$data->instructions['text'] = get_config('local_mxschool', 'advisor_form_instructions');

$form = new local_mxschool\local\advisor_selection\preferences_form();
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    set_config('advisor_form_start_date', generate_timestamp($data, 'start'), 'local_mxschool');
    set_config('advisor_form_stop_date', generate_timestamp($data, 'stop'), 'local_mxschool');
    set_config('advisor_form_enabled_who', $data->who, 'local_mxschool');
    update_notification('advisor_selection_submitted', $data->submitted_subject, $data->submitted_body);
    update_notification('advisor_selection_notify_unsubmitted', $data->unsubmitted_subject, $data->unsubmitted_body);
    update_notification('advisor_selection_notify_results', $data->results_subject, $data->results_body);
    set_config('advisor_form_closing_warning', $data->closing_warning['text'], 'local_mxschool');
    set_config('advisor_form_instructions', $data->instructions['text'], 'local_mxschool');
    logged_redirect(
        $form->get_redirect(), get_string('advisor_selection_preferences_update_success', 'local_mxschool'), 'update'
    );
}

$table = new local_mxschool\local\advisor_selection\faculty_table();

$output = $PAGE->get_renderer('local_mxschool');
$formrenderable = new local_mxschool\output\form($form);
$reportrenderable = new local_mxschool\output\report($table);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($formrenderable);
echo $output->heading(get_string('user_management_faculty_report', 'local_mxschool'));
echo $output->render($reportrenderable);
echo $output->footer();
