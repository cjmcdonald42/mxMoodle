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
 * Audit Report for Healthtest system
 *
 * @package     local_mxschool__healthtest
 * @author      mxMoodle Development Team
 * @copyright   2021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

// 1. is the user signed into moodle
require_login();

// 2. does the user have the capability to view and manage healthtest data
require_capability('local/mxschool:manage_healthpass', context_system::instance());

// Create Filter bar
$filter = new stdClass();
$filter->testing_cycle = optional_param('testing_cycle', '', PARAM_RAW);
$filter->block = optional_param('block', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);
$download = optional_param('download', '', PARAM_ALPHA);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('audit_report', 'healthtest');

// Create table and pass the filters
$table = new local_mxschool\local\healthtest\audit_table($filter, $download);

// Define filter options as an array with value => display
$testing_cycle_options = get_testing_cycle_list();
$block_options = get_healthtest_report_block_options($filter->testing_cycle);

// Define filter options as an array with value => display
$testing_cycle_options = get_testing_cycle_list();
$block_options = get_healthtest_report_block_options($filter->testing_cycle);
$attended_options = array(
	'Present' => get_string('healthtest:test_report:attended:attended', 'local_mxschool'),
	'Absent' => get_string('healthtest:test_report:attended:absent', 'local_mxschool')
);

$buttons = array(
	new local_mxschool\output\redirect_button(
        get_string('healthtest:test_report:appointment', 'local_mxschool'), new moodle_url('/local/mxschool/healthtest/appointment_form.php')
    ),
	new local_mxschool\output\redirect_button(
        get_string('healthtest:test_report:block_report', 'local_mxschool'), new moodle_url('/local/mxschool/healthtest/block_report.php')
    ),
);

// 3. Create an array of userIDs for every user with the capability to access the COVIDtest subsystem.
$healthtest_users = array();
$users = get_user_list();
foreach ($users as $value)
{
    if($value.has_capability('local/mxschool:access_healthtest', context_system::instance())
    {
        array_push($covidtest_users, $value);
    }
}

// Output report to page
$output = $PAGE->get_renderer('local_mxschool');
if ($table->is_downloading()) {
    $renderable = new local_mxschool\output\report_table($table);
    echo $output->render($renderable);
    die();
}
$renderable = new local_mxschool\output\report($table, $filter->search, $dropdowns, $buttons, true);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();









/*$testing_cycle_list = .get_testing_cycle_list();
foreach ($healthtest_users as $user)
{
    foreach ($testing_cycle_list as $list)
    {
        if($user.) // needs if user attended the covid test during the current list
        {
            if(!$list.isempty())
            {
                array_push($list, ", ");
            }
            array_push($list, $list.get_current_testing_cycle()))//need a date inside the method
        }
    }


}
*/
