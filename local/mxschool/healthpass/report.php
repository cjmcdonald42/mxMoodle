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
 * Report for Middlesex's Health Pass system.
 *
 * @package     local_mxschool
 * @subpackage  healthpass
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
require_capability('local/mxschool:manage_healthpass', context_system::instance());

// Creeate filters
$filter = new stdClass();
$filter->status = optional_param('status', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);

setup_mxschool_page('report', 'healthpass');

// Create table and pass the filters
$table = new local_mxschool\local\healthpass\table($filter);

// Define filter options as an array with value => display
$statusoptions = array(
	'Approved' => get_string('healthpass:report:status:approved', 'local_mxschool'),
	'Denied' => get_string('healthpass:report:status:denied', 'local_mxschool'),
	'Unsubmitted' => get_string('healthpass:report:status:unsubmitted', 'local_mxschool')
);

// Create dropdowns, where the last parameter is the default value
$dropdowns = array(
	new local_mxschool\output\dropdown(
	    'status', $statusoptions, $filter->status, get_string('healthpass:report:status:all', 'local_mxschool')
    )
 );

// Create a 'New Healthform' Button
$buttons = array(
     new local_mxschool\output\redirect_button(
         get_string('healthpass:report:add', 'local_mxschool'), new moodle_url('/local/mxschool/healthpass/form.php')
     )
);

// Output report to page
$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\report($table, $filter->search, $dropdowns, $buttons, true);

echo $output->header();
echo $output->heading($PAGE->title);
echo $output->render($renderable);
echo $output->footer();
