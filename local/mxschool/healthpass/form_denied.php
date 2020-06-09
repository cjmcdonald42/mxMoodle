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
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2020 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 require(__DIR__.'/../../../config.php');
 require_once(__DIR__.'/../locallib.php');

 $info = get_todays_healthform_info($USER->id);

 if(!has_capability('local/mxschool:manage_healthpass', context_system::instance())) {
	 if($info->status != 'Denied') redirect(new moodle_url('/my'));
 }

 global $USER;
 $date = generate_datetime();
 echo "
	<body style='background-color:salmon;'>
		<div style='text-align:center;'
		<br><br><br>
		<h1>Health Pass Denied</h1>
		<br><br>
		<h2>{$USER->firstname} {$USER->lastname}</h2>
		<h3>{$date->format('m/d/y')}</h3>
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
