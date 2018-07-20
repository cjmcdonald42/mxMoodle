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
 * Advisor selection preferences page for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage advisor_selection
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once('faculty_table.php');
require_once('preferences_form.php');

require_login();
require_capability('local/mxschool:manage_advisor_selection_preferences', context_system::instance());

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('advisor_selection', 'local_mxschool') => '/local/mxschool/advisor_selection/index.php'
);
$redirect = get_redirect($parents);
$url = '/local/mxschool/advisor_selection/preferences.php';
$title = get_string('advisor_selection_preferences', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$data = new stdClass();
$unsubmitednotification = $DB->get_record('local_mxschool_notification', array('class' => 'advisor_selection_notify_unsubmitted'));
if ($unsubmitednotification) {
    $data->unsubmittedsubject = $unsubmitednotification->subject;
    $data->unsubmittedbody['text'] = $unsubmitednotification->body_html;
}
$resultsnotification = $DB->get_record('local_mxschool_notification', array('class' => 'advisor_selection_notify_results'));
if ($resultsnotification) {
    $data->resultssubject = $resultsnotification->subject;
    $data->resultsbody['text'] = $resultsnotification->body_html;
}
$data->closing_warning['text'] = get_config('local_mxschool', 'advisor_form_closing_warning');
$data->instructions['text'] = get_config('local_mxschool', 'advisor_form_instructions');

$form = new preferences_form();
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    update_notification('advisor_selection_notify_unsubmitted', $data->unsubmittedsubject, $data->unsubmittedbody);
    update_notification('advisor_selection_notify_results', $data->resultssubject, $data->resultsbody);
    set_config('advisor_form_closing_warning', $data->closing_warning['text'], 'local_mxschool');
    set_config('advisor_form_instructions', $data->instructions['text'], 'local_mxschool');
    logged_redirect(
        $form->get_redirect(), get_string('advisor_selection_preferences_edit_success', 'local_mxschool'), 'update'
    );
}

$table = new faculty_table();

$output = $PAGE->get_renderer('local_mxschool');
$formrenderable = new \local_mxschool\output\form($form);
$reportrenderable = new \local_mxschool\output\report($table, 50);

echo $output->header();
echo $output->heading($title);
echo $output->render($formrenderable);
echo $output->heading(get_string('faculty_report', 'local_mxschool'));
echo $output->render($reportrenderable);
echo $output->footer();
