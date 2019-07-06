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
 * Checkin preferences page for Middlesex School's Dorm and Student Functions Plugin.
 *
 * @package    local_mxschool
 * @subpackage checkin
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
require_capability('local/mxschool:manage_checkin_preferences', context_system::instance());

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('checkin', 'local_mxschool') => '/local/mxschool/checkin/index.php'
);
$redirect = get_redirect($parents);
$url = '/local/mxschool/checkin/preferences.php';
$title = get_string('checkin_preferences', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$data = new stdClass();
$data->dormsopen = get_config('local_mxschool', 'dorms_open_date');
$data->secondsemester = get_config('local_mxschool', 'second_semester_start_date');
$data->dormsclose = get_config('local_mxschool', 'dorms_close_date');
$weekends = array();
if ($data->dormsopen && $data->dormsclose) {
    $weekends = generate_weekend_records($data->dormsopen, $data->dormsclose);
    foreach ($weekends as $weekend) {
        $identifier = "weekend_$weekend->id";
        $data->{"{$identifier}_type"} = $weekend->type;
        $data->{"{$identifier}_start"} = $weekend->start_offset;
        $data->{"{$identifier}_end"} = $weekend->end_offset;
    }
}
$submittednotification = get_notification('weekend_form_submitted');
$data->submitted_subject = $submittednotification->subject;
$data->submitted_body['text'] = $submittednotification->body_html;
$approvednotification = get_notification('weekend_form_approved');
$data->approved_subject = $approvednotification->subject;
$data->approved_body['text'] = $approvednotification->body_html;
$data->topinstructions['text'] = get_config('local_mxschool', 'weekend_form_instructions_top');
$data->bottominstructions['text'] = get_config('local_mxschool', 'weekend_form_instructions_bottom');
$data->closedwarning['text'] = get_config('local_mxschool', 'weekend_form_warning_closed');

$form = new preferences_form(array('weekends' => $weekends));
$form->set_redirect($redirect);
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    set_config('dorms_open_date', $data->dormsopen, 'local_mxschool');
    set_config('second_semester_start_date', $data->secondsemester, 'local_mxschool');
    set_config('dorms_close_date', $data->dormsclose, 'local_mxschool');
    foreach ($weekends as $weekend) {
        $identifier = "weekend_{$weekend->id}";
        $weekend->type = $data->{"{$identifier}_type"};
        $weekend->start_offset = $data->{"{$identifier}_start"};
        $weekend->end_offset = $data->{"{$identifier}_end"};
        $DB->update_record('local_mxschool_weekend', $weekend);
    }
    update_notification('weekend_form_submitted', $data->submitted_subject, $data->submitted_body);
    update_notification('weekend_form_approved', $data->approved_subject, $data->approved_body);
    set_config('weekend_form_instructions_top', $data->topinstructions['text'], 'local_mxschool');
    set_config('weekend_form_instructions_bottom', $data->bottominstructions['text'], 'local_mxschool');
    set_config('weekend_form_warning_closed', $data->closedwarning['text'], 'local_mxschool');
    logged_redirect(
        $form->get_redirect(), get_string('checkin_preferences_edit_success', 'local_mxschool'), 'update'
    );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
