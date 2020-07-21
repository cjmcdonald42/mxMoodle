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
 * Interactive check-in sheet for Middlesex's Dorm and Student Functions Plugin.
 *
 * @package     local_mxschool
 * @subpackage  checkin
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../../config.php');
require_once(__DIR__.'/../locallib.php');

require_login();
$isproctor = user_is_student();
if ($isproctor) {
    require_capability('local/mxschool:view_limited_checkin', context_system::instance());
} else {
    require_capability('local/mxschool:view_checkin', context_system::instance());
}

// Ensure that there is a row in local_mxschool_attendance for every student
global $DB;
if($DB->count_records('local_mxschool_attendance') != $DB->count_records('local_mxschool_student')) {
	$record = new stdClass();
	foreach(get_student_list() as $userid=>$name) {
	     $record->userid = $userid;
	     if(!$DB->record_exists('local_mxschool_attendance', array('userid' => $userid))) {
		     $DB->insert_record('local_mxschool_attendance', $record);
	     }
	}
}
$filter = new stdClass();
$filter->dorm = $isproctor ? $DB->get_field('local_mxschool_student', 'dormid', array('userid' => $USER->id))
    : get_param_faculty_dorm();
$filter->attendance = optional_param('attendance', '', PARAM_RAW);

setup_mxschool_page('attendance_report', 'checkin');

$attendanceoptions = array(
	'Attended' => get_string('checkin:attendance_report:attendance:1', 'local_mxschool'),
	'Absent' => get_string('checkin:attendance_report:attendance:0', 'local_mxschool')
);

$table = new local_mxschool\local\checkin\attendance_table($filter);
if ($isproctor) {
    $dropdowns = array();
} else {
    $dropdowns = array(
	    \local_mxschool\output\dropdown::dorm_dropdown($filter->dorm),
	    new local_mxschool\output\dropdown(
		 'attendance', $attendanceoptions, $filter->attendance, get_string('checkin:attendance_report:attendance:all', 'local_mxschool')
	   )
   );
}

$output = $PAGE->get_renderer('local_mxschool');
$renderable = new local_mxschool\output\report($table, null, $dropdowns, array(), true);

echo $output->header();
echo $output->heading(
    get_string('checkin:attendance_report:title', 'local_mxschool', $filter->dorm > 0 ? format_dorm_name($filter->dorm) . ' ' : '')
);
echo $output->render($renderable);
echo $output->footer();
