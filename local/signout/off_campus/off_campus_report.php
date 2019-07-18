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
 * @package    local_signout
 * @subpackage off_campus
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../../mxschool/classes/output/renderable.php');
require_once(__DIR__.'/../../mxschool/classes/mx_dropdown.php');
require_once(__DIR__.'/off_campus_table.php');

require_login();
require_capability('local/signout:manage_off_campus', context_system::instance());

$filter = new stdClass();
$filter->type = optional_param('type', '', PARAM_RAW);
$filter->date = get_param_current_date_off_campus();
$filter->search = optional_param('search', '', PARAM_RAW);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('report', 'off_campus', 'signout');

$types = array(
    'Driver' => get_string('off_campus_report_select_type_driver', 'local_signout'),
    'Passenger' => get_string('off_campus_report_select_type_passenger', 'local_signout'),
    'Parent' => get_string('off_campus_report_select_type_parent', 'local_signout'),
    'Other' => get_string('off_campus_report_select_type_other', 'local_signout')
);
if ($filter->type && !isset($types[$filter->type])) {
    unset($filter->type);
    redirect(new moodle_url($PAGE->url, (array) $filter));
}
if ($action === 'delete' && $id) {
    $record = $DB->get_record('local_signout_off_campus', array('id' => $id));
    $redirect = new moodle_url($PAGE->url, (array) $filter);
    if ($record) {
        $record->deleted = 1;
        $DB->update_record('local_signout_off_campus', $record);
        logged_redirect($redirect, get_string('off_campus_delete_success', 'local_signout'), 'delete');
    } else {
        logged_redirect($redirect, get_string('off_campus_delete_failure', 'local_signout'), 'delete', false);
    }
}

$dates = get_off_campus_date_list();

$table = new off_campus_table($filter);

$dropdowns = array(
    new local_mxschool_dropdown('type', $types, $filter->type, get_string('off_campus_report_select_type_all', 'local_signout')),
    new local_mxschool_dropdown('date', $dates, $filter->date, get_string('off_campus_report_select_date_all', 'local_signout'))
);
$addbutton = new stdClass();
$addbutton->text = get_string('off_campus_report_add', 'local_signout');
$addbutton->url = new moodle_url('/local/signout/off_campus/off_campus_enter.php');

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\report($table, $filter->search, $dropdowns, false, $addbutton);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
