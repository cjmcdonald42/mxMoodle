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
 * Block Report for Middlesex's Health Test system.
 *
 * @package     local_mxschool
 * @subpackage  healthtest
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_healthpass', context_system::instance());

// Creeate filters
$filter = new stdClass();
$filter->testing_cycle = optional_param('testing_cycle', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);
$download = optional_param('download', '', PARAM_ALPHA);
$action = optional_param('action', '', PARAM_RAW);
$id = optional_param('id', 0, PARAM_INT);

setup_mxschool_page('block_report', 'healthtest');

// Action actions
$redirect = new moodle_url($PAGE->url, (array) $filter);
if ($action === 'delete' && $id) {
    $result = $DB->record_exists('local_mxschool_testing_block', array('id' => $id)) ? 'success' : 'failure';
    $DB->delete_records('local_mxschool_testing_block', array('id' => $id));
    $DB->delete_records('local_mxschool_healthtest', array('testing_block_id' => $id));
    logged_redirect(
        $redirect, get_string("deans_permission:report:delete:{$result}", 'local_mxschool'), 'delete', $result === 'success'
    );
}

// Create table and pass the filters
$table = new local_mxschool\local\healthtest\block_table($filter, $download);

// Define filter options as an array with value => display
$testing_cycle_options = get_testing_cycle_list();

$buttons = array(
	new local_mxschool\output\redirect_button(
         get_string('healthtest:block_report:appointment', 'local_mxschool'), new moodle_url('/local/mxschool/healthtest/appointment_form.php')
     ),
	new local_mxschool\output\redirect_button(
         get_string('healthtest:block_report:test_report', 'local_mxschool'), new moodle_url('/local/mxschool/healthtest/test_report.php')
     ),
	new local_mxschool\output\redirect_button(
         get_string('healthtest:block_report:add', 'local_mxschool'), new moodle_url('/local/mxschool/healthtest/block_form.php')
     )
);

// Create dropdowns, where the last parameter is the default value
$dropdowns = array(
	new local_mxschool\output\dropdown(
	    'testing_cycle', $testing_cycle_options, $filter->testing_cycle, get_string('healthtest:test_report:testing_cycle:all', 'local_mxschool')
    )
);

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
