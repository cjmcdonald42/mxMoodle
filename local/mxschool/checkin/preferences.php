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
 * Checkin preferences page for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  checkin
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_checkin_preferences', context_system::instance());

setup_mxschool_page('preferences', 'checkin');

$data = new stdClass();
$data->dorms_open = get_config('local_mxschool', 'dorms_open_date');
$data->second_semester = get_config('local_mxschool', 'second_semester_start_date');
$data->dorms_close = get_config('local_mxschool', 'dorms_close_date');
$weekends = array();
if ($data->dorms_open && $data->dorms_close) {
    $weekends = generate_weekend_records($data->dorms_open, $data->dorms_close);
    foreach ($weekends as $weekend) {
        $identifier = "weekend_$weekend->id";
        $data->{"{$identifier}_type"} = $weekend->type;
        $data->{"{$identifier}_start"} = $weekend->start_offset;
        $data->{"{$identifier}_end"} = $weekend->end_offset;
    }
}
generate_email_preference_fields('weekend_form_submitted', $data, 'submitted');
generate_email_preference_fields('weekend_form_approved', $data, 'approved');
$data->top_instructions['text'] = get_config('local_mxschool', 'weekend_form_instructions_top');
$data->bottom_instructions['text'] = get_config('local_mxschool', 'weekend_form_instructions_bottom');
$data->closed_warning['text'] = get_config('local_mxschool', 'weekend_form_warning_closed');
$weekendtypes = get_weekend_type_list();
$startoptions = get_weekend_start_day_list();
$endoptions = get_weekend_end_day_list();

$form = new local_mxschool\local\checkin\preferences_form(array(
    'weekends' => $weekends, 'weekendtypes' => $weekendtypes, 'startoptions' => $startoptions, 'endoptions' => $endoptions
));
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    set_config('dorms_open_date', $data->dorms_open, 'local_mxschool');
    set_config('second_semester_start_date', $data->second_semester, 'local_mxschool');
    set_config('dorms_close_date', $data->dorms_close, 'local_mxschool');
    foreach ($weekends as $weekend) {
        $identifier = "weekend_{$weekend->id}";
        $weekend->type = $data->{"{$identifier}_type"};
        $weekend->start_offset = $data->{"{$identifier}_start"};
        $weekend->end_offset = $data->{"{$identifier}_end"};
        $DB->update_record('local_mxschool_weekend', $weekend);
    }
    update_notification('weekend_form_submitted', $data, 'submitted');
    update_notification('weekend_form_approved', $data, 'approved');
    set_config('weekend_form_instructions_top', $data->top_instructions['text'], 'local_mxschool');
    set_config('weekend_form_instructions_bottom', $data->bottom_instructions['text'], 'local_mxschool');
    set_config('weekend_form_warning_closed', $data->closed_warning['text'], 'local_mxschool');
    logged_redirect($form->get_redirect(), get_string('checkin:preferences:update:success', 'local_mxschool'), 'update');
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\form($form);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
