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
 * On-campus preferences page for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  on_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/signout:manage_on_campus_preferences', context_system::instance());

$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('preferences', 'on_campus', 'signout');

if ($action === 'delete' && $id) {
    $result = $DB->record_exists('local_signout_location', array('id' => $id)) ? 'success' : 'failure';
    $DB->set_field('local_signout_location', 'deleted', 1, array('id' => $id));
    logged_redirect(
        new moodle_url($PAGE->url, (array) $filter), get_string("on_campus:location:delete:{$result}", 'local_signout'), 'delete',
        $result === 'success'
    );
}

$data = new stdClass();
$data->enabled = get_config('local_signout', 'on_campus_form_enabled');
$data->ip_enabled = get_config('local_signout', 'on_campus_ipvalidation_enabled');
$data->confirmation_enabled = get_config('local_signout', 'on_campus_confirmation_enabled');
$data->refresh = get_config('local_signout', 'on_campus_refresh_rate');
$data->confirmation_undo = get_config('local_signout', 'on_campus_confirmation_undo_window');
$data->ip_form_error['text'] = get_config('local_signout', 'on_campus_form_ipvalidation_error');
$data->ip_sign_in_error_boarder['text'] = get_config('local_signout', 'on_campus_signin_ipvalidation_error_boarder');
$data->ip_sign_in_error_day['text'] = get_config('local_signout', 'on_campus_signin_ipvalidation_error_day');
$data->confirmation['text'] = get_config('local_signout', 'on_campus_form_confirmation');
$data->underclassman_warning['text'] = get_config('local_signout', 'on_campus_form_warning_underclassmen');
$data->junior_warning['text'] = get_config('local_signout', 'on_campus_form_warning_juniors');

$form = new local_signout\local\on_campus\preferences_form();
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    set_config('on_campus_form_enabled', $data->enabled, 'local_signout');
    set_config('on_campus_ipvalidation_enabled', $data->ip_enabled, 'local_signout');
    set_config('on_campus_confirmation_enabled', $data->confirmation_enabled, 'local_signout');
    set_config('on_campus_refresh_rate', $data->refresh, 'local_signout');
    set_config('on_campus_confirmation_undo_window', $data->confirmation_undo, 'local_signout');
    set_config('on_campus_form_ipvalidation_error', $data->ip_form_error['text'], 'local_signout');
    set_config('on_campus_signin_ipvalidation_error_boarder', $data->ip_sign_in_error_boarder['text'], 'local_signout');
    set_config('on_campus_signin_ipvalidation_error_day', $data->ip_sign_in_error_day['text'], 'local_signout');
    set_config('on_campus_form_confirmation', $data->confirmation['text'], 'local_signout');
    set_config('on_campus_form_warning_underclassmen', $data->underclassman_warning['text'], 'local_signout');
    set_config('on_campus_form_warning_juniors', $data->junior_warning['text'], 'local_signout');
    logged_redirect(
        $form->get_redirect(), get_string('on_campus:preferences:update:success', 'local_signout'), 'update'
    );
}

$table = new local_signout\local\on_campus\location_table();
$buttons = array(new local_mxschool\output\redirect_button(
    get_string('on_campus:location_report:add', 'local_signout'), new moodle_url('/local/signout/on_campus/location_edit.php')
));

$output = $PAGE->get_renderer('local_signout');
$renderable = new local_mxschool\output\form($form);
$reportrenderable = new local_mxschool\output\report($table, null, array(), $buttons);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->heading(get_string('on_campus:location_report', 'local_signout'));
echo $output->render($reportrenderable);
echo $output->footer();
