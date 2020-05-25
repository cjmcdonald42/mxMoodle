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

$filter = new stdClass();
$filter->submitted = optional_param('submitted', '', PARAM_RAW);
$filter->user_type = optional_param('user_type', '', PARAM_RAW);
$filter->status = optional_param('status', '', PARAM_RAW);
$filter->search = optional_param('search', '', PARAM_RAW);

setup_mxschool_page('report', 'healthpass');

$table = new local_mxschool\local\healthpass\table($filter);

$submittedoptions = array(
	'1' => get_string('healthpass:report:selectsubmitted:true', 'local_mxschool'),
	'0' => get_string('healthpass:report:selectsubmitted:false', 'local_mxschool')
);
$useroptions = array(
	'student' => get_string('healthpass:report:selectstudents', 'local_mxschool'),
	'facultystaff' => get_string('healthpass:report:selectfaculty', 'local_mxschool')
);
$statusoptions = array(
	'Approved' => get_string('healthpass:report:selectapproved:true', 'local_mxschool'),
	'Denied' => get_string('healthpass:report:selectapproved:false', 'local_mxschool')
);

$dropdowns = array(
	new local_mxschool\output\dropdown(
	    'submitted', $submittedoptions, $filter->submitted, get_string('healthpass:report:selectsubmitted:all', 'local_mxschool')
    ),
	new local_mxschool\output\dropdown(
	    'user_type', $useroptions, $filter->user_type, get_string('healthpass:report:selectall', 'local_mxschool')
    ),
	new local_mxschool\output\dropdown(
	    'status', $statusoptions, $filter->status, get_string('healthpass:report:selectapproved:all', 'local_mxschool')
    )
 );

 $buttons = array(
     new local_mxschool\output\redirect_button(
         get_string('healthpass:report:add', 'local_mxschool'), new moodle_url('/local/mxschool/healthpass/form.php')
     )
);

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\report($table, $filter->search, $dropdowns, $buttons, true);

echo $output->header();
echo $output->heading(
    get_string('healthpass:report', 'local_mxschool')
);
echo $output->render($renderable);
echo $output->footer();
