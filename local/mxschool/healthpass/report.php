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
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
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
$filter->user_type = optional_param('user_type', '', PARAM_RAW);
$filter->dorm = optional_param('dorm', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);
$download = optional_param('download', '', PARAM_ALPHA);

setup_mxschool_page('report', 'healthpass');

if($filter->dorm) {
	$filter->user_type = 'Students';
}

// Create table and pass the filters
$table = new local_mxschool\local\healthpass\table($filter, $download);

// Define filter options as an array with value => display
$statusoptions = array(
	'Approved' => get_string('healthpass:report:status:approved', 'local_mxschool'),
	'Denied' => get_string('healthpass:report:status:denied', 'local_mxschool'),
	'Submitted' => get_string('healthpass:report:status:submitted', 'local_mxschool'),
	'Unsubmitted' => get_string('healthpass:report:status:unsubmitted', 'local_mxschool')
);
$user_type_options = array(
	'Students' => get_string('healthpass:report:user_type:students', 'local_mxschool'),
	'Faculty' => get_string('healthpass:report:user_type:faculty', 'local_mxschool'),
	'Staff' => get_string('healthpass:report:user_type:staff', 'local_mxschool')
);

// Create dropdowns, where the last parameter is the default value
 if($filter->dorm) {
	 $dropdowns = array(
		 new local_mxschool\output\dropdown(
			'status', $statusoptions, $filter->status, get_string('healthpass:report:status:all', 'local_mxschool')
		 ),
		  new local_mxschool\output\dropdown(
		    'user_type', $user_type_options, 'Students', get_string('healthpass:report:status:all', 'local_mxschool')
		 ),
		 local_mxschool\output\dropdown::dorm_dropdown(
			 $filter->dorm
		 )
	 );
 }
 else {
	 $dropdowns = array(
	 	new local_mxschool\output\dropdown(
	 	    'status', $statusoptions, $filter->status, get_string('healthpass:report:status:all', 'local_mxschool')
	     ),
	      new local_mxschool\output\dropdown(
	 	   'user_type', $user_type_options, $filter->user_type, get_string('healthpass:report:status:all', 'local_mxschool')
	     ),
	 	local_mxschool\output\dropdown::dorm_dropdown(
	 		$filter->dorm
	     )
	);
 }

// Create a 'New Healthform' Button
$buttons = array(
     new local_mxschool\output\redirect_button(
         get_string('healthpass:report:add', 'local_mxschool'), new moodle_url('/local/mxschool/healthpass/form.php')
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
