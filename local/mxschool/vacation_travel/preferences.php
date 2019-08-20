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
 * Vacation travel preferences page for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  vacation_travel
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_vacation_travel_preferences', context_system::instance());

$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('preferences', 'vacation_travel');

if ($action === 'delete' && $id) {
    $result = $DB->record_exists('local_mxschool_vt_site', array('id' => $id)) ? 'success' : 'failure';
    $DB->set_field('local_mxschool_vt_site', 'deleted', 1, array('id' => $id));
    logged_redirect(
        new moodle_url($PAGE->url, (array) $filter), get_string("vacation_travel:site:delete:{$result}", 'local_mxschool'),
        'delete', $result === 'success'
    );
}

$data = new stdClass();
$data->start_date = get_config('local_mxschool', 'vacation_form_start_date') ?: get_config('local_mxschool', 'dorms_open_date');
generate_time_selector_fields($data, 'start');
$data->stop_date = get_config('local_mxschool', 'vacation_form_stop_date') ?: get_config('local_mxschool', 'dorms_close_date');
generate_time_selector_fields($data, 'stop');
$data->return_enabled = get_config('local_mxschool', 'vacation_form_returnenabled');
generate_email_preference_fields('vacation_travel_submitted', $data, 'submitted');
generate_email_preference_fields('vacation_travel_notify_unsubmitted', $data, 'unsubmitted');

$form = new local_mxschool\local\vacation_travel\preferences_form();
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    set_config('vacation_form_start_date', generate_timestamp($data, 'start'), 'local_mxschool');
    set_config('vacation_form_stop_date', generate_timestamp($data, 'stop'), 'local_mxschool');
    set_config('vacation_form_returnenabled', $data->return_enabled, 'local_mxschool');
    update_notification('vacation_travel_submitted', $data, 'submitted');
    update_notification('vacation_travel_notify_unsubmitted', $data, 'unsubmitted');
    logged_redirect($form->get_redirect(), get_string('vacation_travel:preferences:update:success', 'local_mxschool'), 'update');
}

$table = new local_mxschool\local\vacation_travel\site_table();
$buttons = array(new local_mxschool\output\redirect_button(
    get_string('vacation_travel:site_report:add', 'local_mxschool'), new moodle_url('/local/mxschool/vacation_travel/site_edit.php')
));

$output = $PAGE->get_renderer('local_mxschool');
$formrenderable = new local_mxschool\output\form($form);
$reportrenderable = new local_mxschool\output\report($table, null, array(), $buttons);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($formrenderable);
echo $output->heading(get_string('vacation_travel:site_report', 'local_mxschool'));
echo $output->render($reportrenderable);
echo $output->footer();
