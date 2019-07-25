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
 * TODO: Description.
 *
 * @package     PACKAGE
 * @author      Jeremiah DeGreeff, Class of 2019 <jrdegreeff@mxschool.edu>
 * @author      Charles J McDonald, Academic Technology Specialist <cjmcdonald@mxschool.edu>
 * @copyright   2019 Middlesex School, 1400 Lowell Rd, Concord MA 01742
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace PACKAGE\task;

defined('MOODLE_INTERNAL') || die;

use \core\task\scheduled_task;

require_once('PATH_TO_PLUGIN_HOME/locallib.php');

class TASK_NAME extends scheduled_task {

    /**
     * @return string The name of the task to be displayed in admin screens.
     */
    public function get_name() {
        return get_string('task_TASK_NAME', 'PACKAGE');
    }

    /**
     * TODO: Description.
     */
    public function execute() {
        // TODO: Execute the task.
    }

}
