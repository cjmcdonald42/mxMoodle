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
 * Class for reminding tomorrow's testers at a specific time every day.
 *
 * @package     local_mxschool
 * @subpackage  healthtest
 * @author      Cannon Caspar, Class of 2021 <cpcaspar@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2021 Middlesex School, 1400 Lowell Rd, Concord MA 01742 All Rights Reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mxschool\task;

defined('MOODLE_INTERNAL') || die();

class healthtest_notify_reminder extends \core\task\scheduled_task {

	/**
      * Return the task's name as shown in admin screens.
      *
      * @return string
      */
     public function get_name() {
         return 'Healthtest Daily Reminders';
     }

     /**
      * Execute the task.
      */
     public function execute() {
		global $CFG;
		require_once($CFG->dirroot . '/local/mxschool/locallib.php');
		require_once($CFG->dirroot . '/local/mxschool/classes/local/healthtest/healthtest_reminder.php');
		if (get_config('local_mxschool', 'healthtest_enabled')=='1') {
			email_tomorrows_testers();
			error_log('THE THE THE THE THE THE THE REMINDER TASK WAS EXECUTED');
		}
     }

}
