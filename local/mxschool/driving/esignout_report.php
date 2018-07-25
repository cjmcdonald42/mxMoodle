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
 * eSignout report for Middlesex School's Dorm and Student functions plugin.
 *
 * @package    local_mxschool
 * @subpackage driving
 * @author     Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author     Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright  2018, Middlesex School, 1400 Lowell Rd, Concord MA
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');
require_once(__DIR__.'/../classes/output/renderable.php');
require_once(__DIR__.'/../classes/mx_dropdown.php');
require_once('esignout_table.php');

require_login();
$isstudent = user_is_student();
if (!$isstudent) {
    require_capability('local/mxschool:manage_esignout', context_system::instance());
}

$filter = new stdClass();
$filter->type = optional_param('type', '', PARAM_RAW);
$filter->date = get_param_current_date_esignout();
$filter->search = optional_param('search', '', PARAM_RAW);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

$parents = array(
    get_string('pluginname', 'local_mxschool') => '/local/mxschool/index.php',
    get_string('driving', 'local_mxschool') => '/local/mxschool/driving/index.php'
);
$url = '/local/mxschool/driving/esignout_report.php';
$title = get_string('esignout_report', 'local_mxschool');

setup_mxschool_page($url, $title, $parents);

$types = array(
    'Driver' => get_string('esignout_report_select_type_driver', 'local_mxschool'),
    'Passenger' => get_string('esignout_report_select_type_passenger', 'local_mxschool'),
    'Parent' => get_string('esignout_report_select_type_parent', 'local_mxschool'),
    'Other' => get_string('esignout_report_select_type_other', 'local_mxschool')
);

if ($filter->type && !isset($types[$filter->type])) {
    redirect(new moodle_url($url, array('type' => '', 'date' => $filter->date, 'search' => $filter->search)));
}
if ($action === 'delete' && $id) {
    $record = $DB->get_record('local_mxschool_esignout', array('id' => $id));
    $urlparams = array('type' => $filter->type, 'date' => $filter->date, 'search' => $filter->search);
    if ($record) {
        $record->deleted = 1;
        $DB->update_record('local_mxschool_esignout', $record);
        logged_redirect(
            new moodle_url($url, $urlparams), get_string('esignout_delete_success', 'local_mxschool'), 'delete'
        );
    } else {
        logged_redirect(
            new moodle_url($url, $urlparams), get_string('esignout_delete_failure', 'local_mxschool'), 'delete', false
        );
    }
}

$dates = get_esignout_date_list();

$table = new esignout_table($filter, $isstudent);

$dropdowns = array(
    new local_mxschool_dropdown('type', $types, $filter->type, get_string('esignout_report_select_type_all', 'local_mxschool'))
);
if (!$isstudent) {
    $dropdowns[] = new local_mxschool_dropdown(
        'date', $dates, $filter->date, get_string('esignout_report_select_date_all', 'local_mxschool')
    );
}
$addbutton = new stdClass();
$addbutton->text = get_string('esignout_report_add', 'local_mxschool');
$addbutton->url = new moodle_url('/local/mxschool/driving/esignout_enter.php');

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new \local_mxschool\output\report($table, $filter->search, $dropdowns, false, $addbutton);

echo $output->header();
echo $output->heading($title);
echo $output->render($renderable);
echo $output->footer();
