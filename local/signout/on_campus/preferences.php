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
    $record = $DB->get_record('local_signout_location', array('id' => $id));
    if ($record) {
        $record->deleted = 1;
        $DB->update_record('local_signout_location', $record);
        logged_redirect($PAGE->url, get_string('on_campus_location_delete_success', 'local_signout'), 'delete');
    } else {
        logged_redirect($PAGE->url, get_string('on_campus_location_delete_failure', 'local_signout'), 'delete', false);
    }
}

$data = new stdClass();
$data->enabled = get_config('local_signout', 'on_campus_form_enabled');
$data->ipenabled = get_config('local_signout', 'on_campus_form_ipenabled');
$data->confirmationenabled = get_config('local_signout', 'on_campus_confirmation_enabled');
$data->refresh = get_config('local_signout', 'on_campus_refresh_rate');
$data->confirmationundo = get_config('local_signout', 'on_campus_confirmation_undo_window');
$data->ipformerror['text'] = get_config('local_signout', 'on_campus_form_iperror');
$data->ipsigninerrorboarder['text'] = get_config('local_signout', 'on_campus_signin_iperror_boarder');
$data->ipsigninerrorday['text'] = get_config('local_signout', 'on_campus_signin_iperror_day');
$data->underclassmanwarning['text'] = get_config('local_signout', 'on_campus_form_warning_underclassmen');
$data->juniorwarning['text'] = get_config('local_signout', 'on_campus_form_warning_juniors');
$data->confirmation['text'] = get_config('local_signout', 'on_campus_form_confirmation');

$form = new local_signout\local\on_campus\preferences_form();
$form->set_data($data);

if ($form->is_cancelled()) {
    redirect($form->get_redirect());
} else if ($data = $form->get_data()) {
    set_config('on_campus_form_enabled', $data->enabled, 'local_signout');
    set_config('on_campus_form_ipenabled', $data->ipenabled, 'local_signout');
    set_config('on_campus_confirmation_enabled', $data->confirmationenabled, 'local_signout');
    set_config('on_campus_refresh_rate', $data->refresh, 'local_signout');
    set_config('on_campus_confirmation_undo_window', $data->confirmationundo, 'local_signout');
    set_config('on_campus_form_iperror', $data->ipformerror['text'], 'local_signout');
    set_config('on_campus_signin_iperror_boarder', $data->ipsigninerrorboarder['text'], 'local_signout');
    set_config('on_campus_signin_iperror_day', $data->ipsigninerrorday['text'], 'local_signout');
    set_config('on_campus_form_warning_underclassmen', $data->underclassmanwarning['text'], 'local_signout');
    set_config('on_campus_form_warning_juniors', $data->juniorwarning['text'], 'local_signout');
    set_config('on_campus_form_confirmation', $data->confirmation['text'], 'local_signout');
    logged_redirect(
        $form->get_redirect(), get_string('on_campus_preferences_edit_success', 'local_signout'), 'update'
    );
}

$table = new local_signout\local\on_campus\location_table();
$buttons = array(new local_mxschool\output\redirect_button(
    get_string('on_campus_location_report_add', 'local_signout'), new moodle_url('/local/signout/on_campus/location_edit.php')
));

$output = $PAGE->get_renderer('local_signout');
$renderable = new local_mxschool\output\form($form);
$reportrenderable = new local_mxschool\output\report($table, null, array(), $buttons);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->heading(get_string('on_campus_location_report', 'local_signout'));
echo $output->render($reportrenderable);
echo $output->footer();
