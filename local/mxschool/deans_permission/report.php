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
 * Deans permission report for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  deans_permission
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_deans_permission', context_system::instance());

$filter = new stdClass();
$filter->advisor = get_param_faculty_advisor();
$filter->status = optional_param('status', '', PARAM_RAW);
$filter->event = optional_param('event', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);
$download = optional_param('download', '', PARAM_ALPHA);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('report', 'deans_permission');

$redirect = new moodle_url($PAGE->url, (array) $filter);
if ($action === 'delete' && $id) {
    $result = $DB->record_exists('local_mxschool_deans_perm', array('id' => $id)) ? 'success' : 'failure';
    $DB->delete_records('local_mxschool_deans_perm', array('id' => $id));
    logged_redirect(
        $redirect, get_string("deans_permission:report:delete:{$result}", 'local_mxschool'), 'delete', $result === 'success'
    );
}
$advisors = get_advisor_list();
$statusoptions = array(
    'approved' => get_string('deans_permission:report:status:approved', 'local_mxschool'),
    'denied' => get_string('deans_permission:report:status:denied', 'local_mxschool'),
    'under_review' => get_string('deans_permission:report:status:under_review', 'local_mxschool')
);
$eventoptions = get_dp_events_list();

$table = new local_mxschool\local\deans_permission\table($filter, $download);
$dropdowns = array(
    new local_mxschool\output\dropdown(
        'advisor', $advisors, $filter->advisor, get_string('report:select_advisor:all', 'local_mxschool')
    ),
    new local_mxschool\output\dropdown(
        'status', $statusoptions, $filter->status, get_string('dropdown:default', 'local_mxschool')
    ),
    new local_mxschool\output\dropdown(
	   'event', $eventoptions, $filter->event, get_string('deans_permission:report:event:all', 'local_mxschool')
    )
);
$buttons = array(
    new local_mxschool\output\redirect_button(
        get_string('deans_permission:report:add', 'local_mxschool'), new moodle_url('/local/mxschool/deans_permission/form.php')
    )
);

$output = $PAGE->get_renderer('local_mxschool');
if ($table->is_downloading()) {
    $renderable = new local_mxschool\output\report_table($table);
    echo $output->render($renderable);
    die();
}
$renderable = new local_mxschool\output\report($table, $filter->search, $dropdowns, $buttons);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
