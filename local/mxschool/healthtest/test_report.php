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
 * Test Report for Middlesex's Health Test system.
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
$filter->day = optional_param('day', '', PARAM_RAW);
$filter->block = optional_param('block', '', PARAM_RAW);
$filter->attended = optional_param('attended', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);
$download = optional_param('download', '', PARAM_ALPHA);

setup_mxschool_page('test_report', 'healthtest');

// Create table and pass the filters
$table = new local_mxschool\local\healthtest\test_table($filter, $download);

// Define filter options as an array with value => display
$day_options = get_healthtest_day_options();
$block_options = get_healthtest_block_options($filter->day);
$attended_options = array(
	'Present' => get_string('healthtest:test_report:attended:attended', 'local_mxschool'),
	'Absent' => get_string('healthtest:test_report:attended:absent', 'local_mxschool')
);

$buttons = array(
	new local_mxschool\output\redirect_button(
         get_string('healthtest:test_report:appointment', 'local_mxschool'), new moodle_url('/local/mxschool/healthpass/appointment_form.php')
     ),
	new local_mxschool\output\redirect_button(
         get_string('healthtest:test_report:block_report', 'local_mxschool'), new moodle_url('/local/mxschool/healthpass/block_report.php')
     ),
);

// Create dropdowns, where the last parameter is the default value
$dropdowns = array(
	new local_mxschool\output\dropdown(
	    'day', $day_options, $filter->day, get_string('healthtest:test_report:day:all', 'local_mxschool')
    ),
    new local_mxschool\output\dropdown(
	   'block', $block_options, $filter->block, get_string('healthtest:test_report:block:all', 'local_mxschool')
    ),
    new local_mxschool\output\dropdown(
        'attended', $attended_options, $filter->attended, get_string('healthtest:test_report:attended:all', 'local_mxschool')
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
