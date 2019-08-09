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
 * Off-campus signout report for Middlesex's eSignout Subplugin.
 *
 * @package     local_signout
 * @subpackage  off_campus
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/signout:manage_off_campus', context_system::instance());

$filter = new stdClass();
$filter->dorm = get_param_faculty_dorm();
$filter->type = optional_param('type', 0, PARAM_INT);
$filter->date = get_param_current_date_off_campus();
$filter->search = optional_param('search', '', PARAM_RAW);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('report', 'off_campus', 'signout');

$types = get_off_campus_type_list() + array(-1 => get_string('off_campus_report_select_type_other', 'local_signout'));
if ($filter->type && !isset($types[$filter->type])) { // Invalid type.
    unset($filter->type);
    redirect(new moodle_url($PAGE->url, (array) $filter));
}
if ($action === 'delete' && $id) {
    $result = $DB->record_exists('local_signout_off_campus', array('id' => $id)) ? 'success' : 'failure';
    $DB->set_field('local_signout_off_campus', 'deleted', 1, array('id' => $id));
    logged_redirect(
        new moodle_url($PAGE->url, (array) $filter), get_string("off_campus_delete_{$result}", 'local_signout'), 'delete',
        $result === 'success'
    );
}

$dates = get_off_campus_date_list();

$table = new local_signout\local\off_campus\table($filter);
$dropdowns = array(
    local_mxschool\output\dropdown::dorm_dropdown($filter->dorm),
    new local_mxschool\output\dropdown(
        'type', $types, $filter->type, get_string('off_campus_report_select_type_all', 'local_signout')
    ),
    new local_mxschool\output\dropdown(
        'date', $dates, $filter->date, get_string('off_campus_report_select_date_all', 'local_signout')
    )
);
$buttons = array(new local_mxschool\output\redirect_button(
    get_string('off_campus_report_add', 'local_signout'), new moodle_url('/local/signout/off_campus/form.php')
));

$output = $PAGE->get_renderer('local_signout');
$renderable = new local_mxschool\output\report($table, $filter->search, $dropdowns, $buttons);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
