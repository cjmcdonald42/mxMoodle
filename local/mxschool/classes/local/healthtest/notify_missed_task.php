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
 * Class for reminding today's missed testers at the end of the day.
 *
 * @package     local_mxschool
 * @subpackage  healthtest
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\local\healthtest;

defined('MOODLE_INTERNAL') || die();

class notify_missed_task extends \core\task\scheduled_task {

	/**
      * Return the task's name as shown in admin screens.
      *
      * @return string
      */
     public function get_name() {
         return 'Healthtest Missed Reminders';
     }

     /**
      * Execute the task.
      */
     public function execute() {
		if (get_config('local_mxschool', 'healthtest_enabled')=='1') {
			$missed_testers = get_todays_missed_tester_list();
			foreach($missed_testers as $tester) {
				(new local_mxschool\local\healthtest\healthtest_missed($tester))->send();
			}
			error_log('CONFIG IS GOOD');
		}
		error_log('THE NOTIFY REMINDER TASK EXECUTE FUNCTION WAS TRIGGERED');
     }

}
