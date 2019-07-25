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
 * Combined on- and off-campus signout report for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/locallib.php');
require_once(__DIR__.'/combined_table.php');

require_login();
require_capability('local/signout:manage_on_campus', context_system::instance());
require_capability('local/signout:manage_off_campus', context_system::instance());

$filter = new stdClass();
$filter->dorm = get_param_faculty_dorm();
$filter->search = optional_param('search', '', PARAM_RAW);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);
$table = optional_param('table', '', PARAM_RAW);

setup_mxschool_page('combined_report', null, 'signout');
$refresh = get_config('local_signout', 'on_campus_refresh_rate');
if ($refresh) {
    $PAGE->set_url(new moodle_url($PAGE->url, (array) $filter));
    $PAGE->set_periodic_refresh_delay((int) $refresh);
}

if ($action === 'delete' && $id && $table) {
    if (!in_array($table, array('on_campus', 'off_campus'))) {
        logged_redirect($PAGE->url, get_string('table_delete_failure', 'local_signout'), 'delete', false);
    }
    $record = $DB->get_record("local_signout_{$table}", array('id' => $id));
    $redirect = new moodle_url($PAGE->url, (array) $filter);
    if ($record) {
        $record->deleted = 1;
        $DB->update_record('local_signout_on_campus', $record);
        logged_redirect($redirect, get_string("{$table}_delete_success", 'local_signout'), 'delete');
    } else {
        logged_redirect($redirect, get_string("{$table}_delete_failure", 'local_signout'), 'delete', false);
    }
}

$table = new combined_table($filter);
$dropdowns = array(\local_mxschool\dropdown::dorm_dropdown($filter->dorm));
$buttons = array(
    new local_mxschool\output\redirect_button(
        get_string('on_campus_report_add', 'local_signout'),
        new moodle_url('/local/signout/on_campus/on_campus_enter.php')
    ),
    new local_mxschool\output\redirect_button(
        get_string('off_campus_report_add', 'local_signout'),
        new moodle_url('/local/signout/off_campus/off_campus_enter.php')
    )
);

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\report($table, $filter->search, $dropdowns, $buttons);

echo $output->header();
echo $output->heading(
    get_string('combined_report_title', 'local_signout', $filter->dorm > 0 ? format_dorm_name($filter->dorm) . ' ' : '')
);
echo $output->render($renderable);
echo $output->footer();
