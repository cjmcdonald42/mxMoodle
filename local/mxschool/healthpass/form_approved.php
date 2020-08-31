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
 * Middlesex's Health Pass Approved Page.
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

 global $USER;
 $info = get_todays_healthform_info($USER->id);

 if(!has_capability('local/mxschool:manage_healthpass', context_system::instance())) {
	 if($info->status != 'Approved') redirect(new moodle_url('/my'));
 }
 $student_info = get_student_contact_info($USER->id);
 if($student_info) {
	 $dorm = $student_info->dorm_name;
	 $b_status = $student_info->boarding_status;
 }

 $date = generate_datetime();
 echo "
	<body style='background-color:lightgreen;'>
		<div style='text-align:center; font-family:Open Sans,sans-serif;'>
		<br><br>
		<h1>COVIDpass Approved</h1>
		<br>
		<h1>{$USER->firstname} {$USER->lastname}</h1>
		";
if(user_is_student()) {
	echo "<h1>{$dorm} ({$b_status})</h1>";
}
echo "
		<h1>{$date->format('m/d/y')}</h1>
		<br><br>
		<form method='get'>
			<button type='submit' name='back'>Back</button>
		</form>
		</div>
	</body>
	";
 if(isset($_GET['back'])) {
	redirect(new moodle_url('/my'));
 }
